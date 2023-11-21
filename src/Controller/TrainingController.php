<?php
// src/Controller/TrainingController.php
namespace App\Controller;

use App\Entity\Member;
use App\Entity\Training;
use App\Entity\TrainingAttendance;
use App\Entity\TrainingSession;
use App\Entity\TrainingSessionAttendance;

use App\Form\TrainingType;

use App\Repository\ClusterMemberRepository;
use App\Repository\MemberRepository;
use App\Repository\TrainingAttendanceRepository;
use App\Repository\TrainingRepository;

use App\Service\Access;
use App\Service\SearchMember;

use DateTime;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\ExpressionLanguage\Expression;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class TrainingController
 *
 * @package App\Controller
 */
#[Route('/stage', name:'training-')]
#[IsGranted(new Expression('is_granted("ROLE_USER")'))]
class TrainingController extends AbstractController
{
    /**
     * @param Access             $access
     * @param TrainingRepository $trainings
     * @param int|null           $year
     *
     * @return Response
     */
    #[Route('/list/{year<\d+>}', name:'list')]
    public function list(Access $access, TrainingRepository $trainings, ?int $year = null): Response
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

        for ($i = intval($today->format('Y')) + 1; $i >= 2005; $i--)
        {
            $data['YearList'][] = $i;
        }

        $data['Trainings'] = $trainings->getTraining($year);

        $data['Year'] = $year;

        return $this->render('Training/list.html.twig', array('data' => $data));
    }

    /**
     * @param Access                 $access
     * @param EntityManagerInterface $em
     * @param Request                $request
     *
     * @return Response
     */
    #[Route('/ajouter-un-stage', name:'trainingAdd')]
    public function trainingAdd(Access $access, EntityManagerInterface $em, Request $request): Response
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

            $duration = date_diff($session->getTrainingSessionEnd(), $session->getTrainingSessionStart());

            $session->setTrainingSessionDuration($duration->h * 60 + $duration->i);

            $training->addTrainingSessions($session);

            $em->persist($training);
            $em->flush();

            return $this->redirectToRoute('training-list');
        }

        return $this->render('/Training/Modal/trainingAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access                 $access
     * @param EntityManagerInterface $em
     * @param Request                $request
     * @param Training               $training
     *
     * @return RedirectResponse|Response
     */
    #[Route('/{training<\d+>}/modifier', name:'trainingEdit')]
    public function trainingEdit(Access $access, EntityManagerInterface $em, Request $request, Training $training): RedirectResponse|Response
    {
        if (!$access->check('Training-Edit'))
        {
            die();
        }

        $formDelete = $this->createForm(TrainingType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('training-trainingEdit', array('training' => $training->getTrainingId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $em->remove($training);
            $em->flush();

            return $this->redirectToRoute('training-list');
        }

        $formEdit = $this->createForm(TrainingType::class, $training, array('formData' => array('Form' => 'Training', 'Action' => 'Edit'), 'data_class' => Training::class, 'action' => $this->generateUrl('training-trainingEdit', array('training' => $training->getTrainingId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $em->flush();

            return $this->redirectToRoute('training-index', array('training' => $training->getTrainingId()));
        }

        return $this->render('Training/Modal/trainingEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access                       $access
     * @param MemberRepository             $members
     * @param Request                      $request
     * @param SearchMember                 $search
     * @param Session                      $session
     * @param Training                     $training
     * @param TrainingAttendanceRepository $attendances
     *
     * @return RedirectResponse|Response
     */
    #[Route('/{training<\d+>}', name:'index')]
    public function index(Access $access, MemberRepository $members, Request $request, SearchMember $search, Session $session, Training $training, TrainingAttendanceRepository $attendances): RedirectResponse|Response
    {
        if (!($access->check('Training-Index')))
        {
            die();
        }

        if (!$session->has('activeTrainingTab'))
        {
            $session->set('activeTrainingTab', 'attendanceTab');
        }

        $data['Training'] = $training;

        $form = $this->createForm(TrainingType::class, null, array('formData' => array('Form' => 'Search')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (ctype_digit($form->get('Search')->getData()))
            {
                $member = $members->findOneBy(['memberId' => $form->get('Search')->getData()]);

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

                $data['SearchForeign'] = $attendances->getFullSearchMembers($form->get('Search')->getData(), $training->getTrainingId());
            }
        }

        return $this->render('Training/index.html.twig', array('form' => $form->createView(), 'data' => $data));
    }

    /**
     * @param Training $training
     * @param Session  $session
     *
     * @return Response
     */
    #[Route('/{training<\d+>}/onglet-cours', name:'lessonTab')]
    public function lessonTab(Training $training, Session $session): Response
    {
        $session->set('activeTrainingTab', 'lessonTab');

        $data['Training'] = $training;

        return $this->render('Training/Tab/lessonTab.html.twig', array('data' => $data));
    }

    /**
     * @param ClusterMemberRepository      $clusters
     * @param Session                      $session
     * @param Training                     $training
     * @param TrainingAttendanceRepository $attendances
     *
     * @return Response
     */
    #[Route('/{training<\d+>}/onglet-caisse', name:'moneyTab')]
    public function moneyTab(ClusterMemberRepository $clusters, Session $session, Training $training, TrainingAttendanceRepository $attendances): Response
    {
        $session->set('activeTrainingTab', 'moneyTab');

        $data['Training'] = $training;

        if ($training->getTrainingStatus() == 2)
        {
            foreach ($clusters->getFreeTrainingMember($training->getTrainingFirstDate()) as $member)
            {
                $freeMember[] = $member['Id'];
            }
        }

        $data['Attendances'] = array();

        $members = $attendances->getTrainingAttendances($training);

        foreach ($members as $member)
        {
            $member['StatusText'] = $member['Status'] == 1 ? 'Validée' : 'Pré-inscrit';

            is_null($member['FullName']) ? $member['FullName'] = ucwords(strtolower($member['Firstname'] . ' ' . $member['Name'])) : $member['FullName'] = ucwords(strtolower($member['FullName']));

            !is_null($member['Id']) ?: $member['Id'] = 'N/A';

            if (($training->getTrainingStatus() == 1) || (isset($freeMember) && is_int(array_search($member['Id'], $freeMember))))
            {
                $member['CardText']      = 'Gratuit';
                $member['CashText']      = 'Gratuit';
                $member['DiscountText']  = 'Gratuit';
                $member['TransfertText'] = 'Gratuit';
                $member['TotalText']     = 'Gratuit';
            }
            else
            {
                $member['Total'] = $member['Card'] + $member['Cash'] + $member['Transfert'];

                $member['CardText']      = number_format($member['Card'] / 100, 2, ",") . ' €';
                $member['CashText']      = number_format($member['Cash'] / 100, 2, ",") . ' €';
                $member['DiscountText']  = number_format($member['Discount'] / 100, 2, ",") . ' €';
                $member['TransfertText'] = number_format($member['Transfert'] / 100, 2, ",") . ' €';
                $member['TotalText']     = number_format($member['Total'] / 100, 2, ",") . ' €';
            }

            $data['Attendances'][] = $member;
        }

        return $this->render('Training/Tab/moneyTab.html.twig', array('data' => $data));
    }

    /**
     * @param MemberRepository             $members
     * @param Session                      $session
     * @param Training                     $training
     * @param TrainingAttendanceRepository $attendances
     *
     * @return Response
     */
    #[Route('/{training<\d+>}/onglet-presence', name:'attendanceTab')]
    public function attendanceTab(MemberRepository $members, Session $session, Training $training, TrainingAttendanceRepository $attendances): Response
    {
        $session->set('activeTrainingTab', 'attendanceTab');

        $data['Training'] = $training;

        $data['Attendances']['AFA']         = array();
        $data['Attendances']['Foreign']     = array();
        $data['Attendances']['ForeignList'] = false;

        if ($session->has('Club'))
        {
            foreach ($members->getClubActiveMemberList($session->get('Club'), null, $training->getTrainingFirstDate()) as $member)
            {
                $clubMembers[] = $member->getMemberId();
            }
        }

        $members = $attendances->getTrainingAttendances($training);

        foreach ($members as $member)
        {
            if (($session->has('Club') && !is_int(array_search($member['Id'], $clubMembers))) || $member['Attendance'] == 0)
            {
                continue;
            }

            $member['Status'] = $member['Status'] == 1 ? 'Validé' : 'Pré-inscrit';

            is_null($member['FullName']) ? $member['FullName'] = ucwords(strtolower($member['Firstname'] . ' ' . $member['Name'])) : $member['FullName'] = ucwords(strtolower($member['FullName']));

            !is_null($member['Id']) ?: $member['Id'] = 'N/A';

            $member['Id'] == 'N/A' && $member['Attendance'] > 0 ? $data['Attendances']['Foreign'][] = $member : $data['Attendances']['AFA'][] = $member;
        }

        if (sizeof($data['Attendances']['Foreign']) > 0)
        {
            $data['Attendances']['ForeignList']  = true;
            $data['Attendances']['ForeignCount'] = sizeof($data['Attendances']['Foreign']);

            usort($data['Attendances']['Foreign'], function($a, $b) { return $a['FullName'] > $b['FullName'];});
        }

        $data['Attendances']['AFACount'] = sizeof($data['Attendances']['AFA']);

        usort($data['Attendances']['AFA'], function($a, $b) { return $a['FullName'] > $b['FullName'];});

        return $this->render('Training/Tab/attendanceTab.html.twig', array('data' => $data));
    }

    /**
     * @param Access                 $access
     * @param EntityManagerInterface $em
     * @param Request                $request
     * @param Training               $training
     *
     * @return Response
     */
    #[Route('/{training<\d+>}/ajouter-un-cours', name:'sessionAdd')]
    public function sessionAdd(Access $access, EntityManagerInterface $em, Request $request, Training $training): Response
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

            $duration = date_diff($session->getTrainingSessionEnd(), $session->getTrainingSessionStart());

            $session->setTrainingSessionDuration($duration->h * 60 + $duration->i);
            $session->setTrainingSessionTraining($training);

            $em->persist($session);
            $em->flush();

            return $this->redirectToRoute('training-index', array('training' => $training->getTrainingId()));
        }

        return $this->render('/Training/Modal/sessionAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access                 $access
     * @param EntityManagerInterface $em
     * @param Request                $request
     * @param Training               $training
     * @param TrainingSession        $session
     *
     * @return RedirectResponse|Response
     */
    #[Route('/{training<\d+>}/modifier-un-cours/{session<\d+>}', name:'sessionEdit')]
    public function sessionEdit(Access $access, EntityManagerInterface $em, Request $request, Training $training, TrainingSession $session): RedirectResponse|Response
    {
        if (!$access->check('Training-SessionEdit'))
        {
            die();
        }

        $formDelete = $this->createForm(TrainingType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('training-sessionEdit', array('training' => $training->getTrainingId(), 'session' => $session->getTrainingSessionId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $em->remove($session);
            $em->flush();

            return $this->redirectToRoute('training-index', array('training' => $training->getTrainingId()));
        }

        $formEdit = $this->createForm(TrainingType::class, $session, array('formData' => array('Form' => 'Session', 'Action' => 'Edit'), 'data_class' => TrainingSession::class, 'action' => $this->generateUrl('training-sessionEdit', array('training' => $training->getTrainingId(), 'session' => $session->getTrainingSessionId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $em->flush();

            return $this->redirectToRoute('training-index', array('training' => $training->getTrainingId()));
        }

        return $this->render('Training/Modal/sessionEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access                       $access
     * @param EntityManagerInterface       $em
     * @param MemberRepository             $members
     * @param Request                      $request
     * @param Training                     $training
     * @param TrainingAttendanceRepository $attendances
     * @param TrainingAttendance|null      $attendance
     *
     * @return RedirectResponse|Response
     */
    #[Route('/{training<\d+>}/ajouter-pratiquant/{attendance}', name:'attendance')]
    public function attendance(Access $access, EntityManagerInterface $em, MemberRepository $members, Request $request, Training $training, TrainingAttendanceRepository $attendances, ?TrainingAttendance $attendance = null): RedirectResponse|Response
    {
        if (!($access->check('Training-FullAccess')))
        {
            if (!($access->check('Training-AttendanceAdd')) && $training->getTrainingLastDate() < new DateTime('+7 day today'))
            {
                die();
            }
        }

        $formData['Delete']   = false;
        $formData['Training'] = $training;

        $formDelete = null;

        if (!is_null($attendance))
        {
            $formData['Delete'] = true;

            $formDelete = $this->createForm(TrainingType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('training-attendance', array('training' => $formData['Training']->getTrainingId(), 'attendance' => $attendance->getTrainingAttendanceId()))));

            $formDelete->handleRequest($request);

            if ($formDelete->isSubmitted() && $formDelete->isValid())
            {
                $em->remove($attendance);
                $em->flush();

                return $this->redirectToRoute('training-index', array('training' => $formData['Training']->getTrainingId()));
            }
        }

        $formData['Edit'] = true;

        if (!is_null($attendance))
        {
            $formData['Member'] = $attendance->getTrainingAttendanceMember();
        }
        elseif ($request->query->has('Member'))
        {
            $formData['Member'] = $members->findOneBy(['memberId' => intval($request->query->get('Member'))]);

            $attendance = $attendances->findOneBy(['trainingAttendanceTraining' => $formData['Training']->getTrainingId(), 'trainingAttendanceMember' => $formData['Member']->getMemberId()]);

            if (is_null($attendance))
            {
                $attendance = new TrainingAttendance();

                $formData['Edit'] = false;
            }
        }
        else
        {
            $formData['Member'] = null;

            $attendance = new TrainingAttendance();

            if ($request->query->has('Name'))
            {
                $attendance->setTrainingAttendanceName(html_entity_decode($request->query->get('Name')));
            }

            $formData['Edit'] = false;
        }

        $formData['Choices']     = $formData['Training']->getTrainingSessions();
        $formData['Foreign']     = is_null($formData['Member']);
        $formData['Form']        = 'Attendance';
        $formData['Free']        = ($formData['Training']->getTrainingStatus() == 1) || ($formData['Member']?->getMemberFreeTraining());
        $formData['Lessons']     = count($formData['Choices']) > 1;
        $formData['Status']      = new DateTime('+1 day today') >= $formData['Training']->getTrainingFirstDate();
        $formData['Total']       = number_format($formData['Edit'] ? $attendance->getTrainingAttendancePaymentTotal() / 100 : 0, 2, ',') . ' €';
        $formData['OldSessions'] = array();

        if ($formData['Edit'])
        {
            foreach ($attendance->getTrainingAttendanceSessions() as $sessionAttendance)
            {
                $formData['OldSessions'][] = $sessionAttendance->getTrainingSessionAttendanceTrainingSession();
            }
        }

        $urlParam['training'] = $formData['Training']->getTrainingId();
        $urlParam['attendance'] = $formData['Edit'] ? $attendance->getTrainingAttendanceId() : null;
        $urlParam['Member'] = is_null($formData['Member']) ? null : $formData['Member']->getMemberId();

        $form = $this->createForm(TrainingType::class, $attendance, array('formData' => $formData, 'data_class' => TrainingAttendance::class, 'action' => $this->generateUrl('training-attendance', $urlParam)));

        if ($formData['Lessons'])
        {
            $form->get('TrainingAttendanceSessions')->setData($formData['OldSessions']);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (!$formData['Edit'])
            {
                $attendance->setTrainingAttendanceTraining($formData['Training']);
                $attendance->setTrainingAttendanceMember($formData['Member']);

                $em->persist($attendance);
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

            foreach ($attendance->getTrainingAttendanceSessions() as $sessionAttendance)
            {
                $em->remove($sessionAttendance);
                $em->flush();
            }

            if (count($formData['Choices']) == 1)
            {
                $sessionAttendance = new TrainingSessionAttendance();

                $sessionAttendance->setTrainingSessionAttendanceTrainingAttendance($attendance);
                $sessionAttendance->setTrainingSessionAttendanceTrainingSession($formData['Choices'][0]);

                $em->persist($sessionAttendance);
            }
            else
            {
                foreach ($form->get('TrainingAttendanceSessions')->getData() as $session)
                {
                    $sessionAttendance = new TrainingSessionAttendance();

                    $sessionAttendance->setTrainingSessionAttendanceTrainingAttendance($attendance);
                    $sessionAttendance->setTrainingSessionAttendanceTrainingSession($session);

                    $em->persist($sessionAttendance);
                }
            }

            $em->flush();

            return $this->redirectToRoute('training-index', array('training' => $formData['Training']->getTrainingId()));
        }

        return $this->render('Training/Modal/attendance.html.twig', array('form' => $form->createView(), 'formDelete' => $formDelete?->createView(), 'formData' => $formData));
    }

    /**
     * @param Access                       $access
     * @param EntityManagerInterface       $em
     * @param Member                       $member
     * @param Request                      $request
     * @param Training                     $training
     * @param TrainingAttendanceRepository $attendances
     *
     * @return Response
     */
    #[Route('/{training<\d+>}/ajouter-une-ristourne/{member<\d+>}', name:'discountAdd')]
    public function discountAdd(Access $access, EntityManagerInterface $em, Member $member, Request $request, Training $training, TrainingAttendanceRepository $attendances): Response
    {
        if (!$access->check('Training-DiscountAdd'))
        {
            die();
        }

        $attendance = $attendances->findOneBy(array('trainingAttendanceTraining' => $training->getTrainingId(), 'trainingAttendanceMember' => $member->getMemberId()));

        if (is_null($attendance))
        {
            $attendance = new TrainingAttendance();

            $attendance->setTrainingAttendanceMember($member);
            $attendance->setTrainingAttendanceTraining($training);
            $attendance->setTrainingAttendanceStatus(2);

            $em->persist($attendance);
        }

        $form = $this->createForm(TrainingType::class, $attendance, array('formData' => array('Form' => 'Discount'), 'data_class' => TrainingAttendance::class, 'action' => $this->generateUrl('training-discountAdd', array('training' => $training->getTrainingId(), 'member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $em->flush();

            return $this->redirectToRoute('training-index', array('training' => $training->getTrainingId()));
        }

        return $this->render('/Training/Modal/discountAdd.html.twig', array('form' => $form->createView()));
    }
}
