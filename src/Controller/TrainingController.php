<?php
// src/Controller/TrainingController.php
namespace App\Controller;

use App\Entity\Member;
use App\Entity\Training;
use App\Entity\TrainingAttendance;
use App\Entity\TrainingSession;
use App\Entity\TrainingSessionAttendance;

use App\Form\TrainingType;

use App\Repository\TrainingRepository;

use App\Service\Access;
use App\Service\SearchMember;

use DateTime;

use Doctrine\Persistence\ManagerRegistry;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TrainingController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
#[Route('/stage', name:'training-')]
class TrainingController extends AbstractController
{
    /**
     * @param Access $access
     * @param TrainingRepository $trainingRepository
     * @param int|null $year
     * @return Response
     */
    #[Route('/list/{year<\d+>}', name:'list')]
    public function list(Access $access, TrainingRepository $trainingRepository, ?int $year = null): Response
    {
        if (!($access->check('Training-Menu')))
        {
            die();
        }

        $today = new DateTime();

        if (is_null($year))
        {
            $year = intval($today->format('Y'));
        }

        $data['YearList'] = array();

        for ($i = intval($today->format('Y')); $i >= 2005; $i--)
        {
            $data['YearList'][] = $i;
        }

        $data['Trainings'] = $trainingRepository->getTraining($year);

        $data['Year'] = $year;

        return $this->render('Training/list.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param SearchMember $search
     * @param Training $training
     * @return RedirectResponse|Response
     */
    #[Route('/{training<\d+>}', name:'index')]
    public function index(Access $access, ManagerRegistry $doctrine, Request $request, SearchMember $search, Training $training): RedirectResponse|Response
    {
        if (!($access->check('Training-Index')))
        {
            die();
        }

        $data['Training'] = $training;

        $form = $this->createForm(TrainingType::class, null, array('formData' => array('Form' => 'Search')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (ctype_digit($form->get('Search')->getData()))
            {
                $member = $doctrine->getRepository(Member::class)->findOneBy(['member_id' => $form->get('Search')->getData()]);

                if (is_null($member))
                {
                    $this->addFlash('warning', 'Ce numéro licence n\'existe pas');

                    return $this->redirectToRoute('training-index', array('training' => $training->getTrainingId()));
                }
                else if ($member->getMemberOutdate())
                {
                    $this->addFlash('warning', 'Ce membre n\'est plus en ordre de licence depuis plus de trois mois');

                    return $this->redirectToRoute('training-index', array('training' => $training->getTrainingId()));
                }
                else
                {
                    $data['SearchMember'] = $search->getResults($form->get('Search')->getData());
                }
            }
            else
            {
                $data['Search'] = htmlentities($form->get('Search')->getData());

                $data['SearchMember'] = $search->getResults($form->get('Search')->getData());

                $data['SearchForeign'] = $doctrine->getRepository(TrainingAttendance::class)->getFullSearchMembers($form->get('Search')->getData(), $training->getTrainingId());
            }
        }

        return $this->render('Training/index.html.twig', array('form' => $form->createView(), 'data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @param Training $training
     * @return Response
     */
    #[Route('/{training<\d+>}/details-des-presences', name:'attendancesDetails')]
    public function attendancesDetails(Access $access, ManagerRegistry $doctrine, Request $request, Session $session, Training $training): Response
    {
        if (!$access->check('Training-AttendancesDetails'))
        {
            die();
        }

        $data['Training'] = $training;

        if ($session->has('Club'))
        {
            $data['Attendances'] = array();

            foreach ($training->getTrainingAttendances() as $attendance)
            {
                if (!is_null($attendance->getTrainingAttendanceMember()))
                {
                    if ($attendance->getTrainingAttendanceMember()->getMemberActualClub()->getClubId() === $session->get('Club')->getClubId())
                    {
                        $data['Attendances'][] = $attendance;
                    }
                }
            }
        }
        else
        {
            $data['Offset'] = max(0, $request->query->getInt('offset'));

            $data['Previous'] = $data['Offset'] - $doctrine->getRepository(TrainingAttendance::class)->attendancesPerPage;

            $data['Attendances'] = $doctrine->getRepository(TrainingAttendance::class)->getTrainingAttendances($training, $data['Offset']);

            $data['Next'] = min(count($data['Attendances']), $data['Offset'] + $doctrine->getRepository(TrainingAttendance::class)->attendancesPerPage);

            $data['Last'] = intdiv(count($training->getTrainingAttendances()), $doctrine->getRepository(TrainingAttendance::class)->attendancesPerPage) * $doctrine->getRepository(TrainingAttendance::class)->attendancesPerPage;
        }

        return $this->render('/Training/Tabs/attendancesDetails.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/ajouter-un-stage', name:'trainingAdd')]
    public function trainingAdd(Access $access, ManagerRegistry $doctrine, Request $request): Response
    {
        if (!$access->check('Training-Add'))
        {
            die();
        }

        $form = $this->createForm(TrainingType::class, new Training, array('formData' => array('Form' => 'Training', 'Action' => 'Add'), 'data_class' => Training::class, 'action' => $this->generateUrl('training-trainingAdd')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $training = $form->getData();

            $session = $training->getSession();

            $duration = date_diff($session->getTrainingSessionEndingHour(), $session->getTrainingSessionStartingHour());

            $session->setTrainingSessionDuration($duration->h * 60 + $duration->i);

            $training->addTrainingSessions($session);
            $training->setTrainingType(1);

            $entityManager = $doctrine->getManager();

            $entityManager->persist($training);
            $entityManager->flush();

            return $this->redirectToRoute('training-list');
        }

        return $this->render('/Training/Modal/trainingAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Training $training
     * @return RedirectResponse|Response
     */
    #[Route('/{training<\d+>}/modifier', name:'trainingEdit')]
    public function trainingEdit(Access $access, ManagerRegistry $doctrine, Request $request, Training $training): RedirectResponse|Response
    {
        if (!$access->check('Training-Edit'))
        {
            die();
        }

        $formDelete = $this->createForm(TrainingType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('training-trainingEdit', array('training' => $training->getTrainingId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($training);
            $entityManager->flush();

            return $this->redirectToRoute('training-list');
        }

        $formEdit = $this->createForm(TrainingType::class, $training, array('formData' => array('Form' => 'Training', 'Action' => 'Edit'), 'data_class' => Training::class, 'action' => $this->generateUrl('training-trainingEdit', array('training' => $training->getTrainingId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('training-index', array('training' => $training->getTrainingId()));
        }

        return $this->render('Training/Modal/trainingEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Training $training
     * @return Response
     */
    #[Route('/{training<\d+>}/ajouter-un-cours', name:'sessionAdd')]
    public function sessionAdd(Access $access, ManagerRegistry $doctrine, Request $request, Training $training): Response
    {
        if (!$access->check('Training-SessionAdd'))
        {
            die();
        }

        $form = $this->createForm(TrainingType::class, new TrainingSession, array('formData' => array('Form' => 'Session', 'Action' => 'Add'), 'data_class' => TrainingSession::class, 'action' => $this->generateUrl('training-sessionAdd', array('training' => $training->getTrainingId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $session = $form->getData();

            $duration = date_diff($session->getTrainingSessionEndingHour(), $session->getTrainingSessionStartingHour());

            $session->setTrainingSessionDuration($duration->h * 60 + $duration->i);
            $session->setTraining($training);

            $entityManager = $doctrine->getManager();

            $entityManager->persist($session);
            $entityManager->flush();

            return $this->redirectToRoute('training-list');
        }

        return $this->render('/Training/Modal/sessionAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Training $training
     * @param TrainingSession $session
     * @return RedirectResponse|Response
     */
    #[Route('/{training<\d+>}/modifier-un-cours/{session<\d+>}', name:'sessionEdit')]
    public function sessionEdit(Access $access, ManagerRegistry $doctrine, Request $request, Training $training, TrainingSession $session): RedirectResponse|Response
    {
        if (!$access->check('Training-SessionEdit'))
        {
            die();
        }

        $formDelete = $this->createForm(TrainingType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('training-sessionEdit', array('training' => $training->getTrainingId(), 'session' => $session->getTrainingSessionId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($session);
            $entityManager->flush();

            return $this->redirectToRoute('training-attendanceForm', array('training' => $training->getTrainingId()));
        }

        $formEdit = $this->createForm(TrainingType::class, $session, array('formData' => array('Form' => 'Session', 'Action' => 'Edit'), 'data_class' => TrainingSession::class, 'action' => $this->generateUrl('training-sessionEdit', array('training' => $training->getTrainingId(), 'session' => $session->getTrainingSessionId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('training-attendanceForm', array('training' => $training->getTrainingId()));
        }

        return $this->render('Training/Modal/sessionEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Training $training
     * @param TrainingAttendance|null $attendance
     * @return RedirectResponse|Response
     */
    #[Route('/{training<\d+>}/ajouter-pratiquant/{attendance}', name:'attendance')]
    public function attendance(Access $access, ManagerRegistry $doctrine, Request $request, Training $training, ?TrainingAttendance $attendance = null): RedirectResponse|Response
    {
        if (!($access->check('Training-FullAccess')))
        {
            if (!($access->check('Training-AttendanceAdd')) && $training->getTrainingLastDate() < new DateTime('+7 day today'))
            {
                die();
            }
        }

        $formData['Edit'] = true;

        if (!is_null($attendance))
        {
            $member = $attendance->getTrainingAttendanceMember();
        }
        elseif ($request->query->has('Member'))
        {
            $member = $doctrine->getRepository(Member::class)->findOneBy(['member_id' => intval($request->query->get('Member'))]);

            $attendance = $doctrine->getRepository(TrainingAttendance::class)->findOneBy(['training' => $training->getTrainingId(), 'training_attendance_member' => $member->getMemberId()]);

            if (is_null($attendance))
            {
                $attendance = new TrainingAttendance();

                $formData['Edit'] = false;
            }
        }
        else
        {
            $member = null;

            $attendance = new TrainingAttendance();

            if ($request->query->has('Name'))
            {
                $attendance->setTrainingAttendanceName(html_entity_decode($request->query->get('Name')));
            }

            $formData['Edit'] = false;
        }

        $formData['Form']      = 'Attendance';
        $formData['Free']      = ($training->getTrainingStatus() == 1) || ($member?->getMemberFreeTraining());
        $formData['Foreign']   = is_null($member);
        $formData['Choices']   = $doctrine->getRepository(TrainingSession::class)->findBy(['training' => $training->getTrainingId()], ['training_session_date' => 'ASC', 'training_session_starting_hour' => 'ASC', 'training_session_duration' => 'ASC']);
        $formData['Lessons']   = count($formData['Choices']) > 1;
        $formData['Subscribe'] = new DateTime('+1 day today') >= $training->getTrainingFirstDate();

        $sessionAttendances = array();

        $oldSessions = array();

        if ($formData['Edit'])
        {
            $sessionAttendances = $doctrine->getRepository(TrainingSessionAttendance::class)->findBy(['training_session_attendances' => $attendance->getTrainingAttendanceId()]);

            foreach ($sessionAttendances as $sessionAttendance)
            {
                $oldSessions[] = $sessionAttendance->getTrainingSession();
            }
        }

        $urlParam['training'] = $training->getTrainingId();
        $formData['Edit'] ? $urlParam['attendance'] = $attendance->getTrainingAttendanceId() : $urlParam['attendance'] = 'null';
        is_null($member) ?: $urlParam['Member'] = $member->getMemberId();

        $form = $this->createForm(TrainingType::class, $attendance, array('formData' => $formData, 'data_class' => TrainingAttendance::class, 'action' => $this->generateUrl('training-attendance', $urlParam)));

        if ($formData['Lessons'])
        {
            $form->get('TrainingAttendanceSessions')->setData($oldSessions);
        }

        $form->get('TrainingAttendanceSubscribe')->setData($formData['Subscribe'] ? 1 : 2);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            if (!$formData['Edit'])
            {
                $attendance->setTraining($training);
                $attendance->setTrainingAttendanceMember($member);

                $entityManager->persist($attendance);
            }

            if (!$formData['Free'])
            {
                if ($form->get('TrainingAttendancePaymentType')->getData() == 1)
                {
                    $attendance->setTrainingAttendancePaymentCash($form->get('TrainingAttendancePayment')->getData() + $attendance->getTrainingAttendancePaymentCash());
                }
                elseif ($form->get('TrainingAttendancePaymentType')->getData() == 2)
                {
                    $attendance->setTrainingAttendancePaymentCard($form->get('TrainingAttendancePayment')->getData() + $attendance->getTrainingAttendancePaymentCard());
                }
                else
                {
                    $attendance->setTrainingAttendancePaymentTransfert($form->get('TrainingAttendancePayment')->getData() + $attendance->getTrainingAttendancePaymentTransfert());
                }
            }

            foreach ($sessionAttendances as $sessionAttendance)
            {
                $entityManager->remove($sessionAttendance);
            }

            if (count($formData['Choices']) == 1)
            {
                $sessionAttendance = new TrainingSessionAttendance();

                $sessionAttendance->setTrainingSessionAttendances($attendance);
                $sessionAttendance->setTrainingSession($formData['Choices'][0]);

                $entityManager->persist($sessionAttendance);
            }
            else
            {
                foreach ($form->get('TrainingAttendanceSessions')->getData() as $session)
                {
                    $sessionAttendance = new TrainingSessionAttendance();

                    $sessionAttendance->setTrainingSessionAttendances($attendance);
                    $sessionAttendance->setTrainingSession($session);

                    $entityManager->persist($sessionAttendance);
                }
            }

            $attendance->setTrainingAttendanceStatus($form->get('TrainingAttendanceSubscribe')->getData());

            $entityManager->flush();

            return $this->redirectToRoute('training-index', array('training' => $training->getTrainingId()));
        }

        return $this->render('Training/Modal/attendance.html.twig', array('form' => $form->createView(), 'member' => $member, 'lessons' => $formData['Lessons'], 'training' => $training, 'edit' => $formData['Edit'], 'total' => $formData['Edit'] ? $attendance->getTrainingAttendancePaymentTotal() : 0, 'free' => $formData['Free']));
    }
}
