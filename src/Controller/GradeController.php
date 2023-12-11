<?php
// src/Controller/GradeController.php
namespace App\Controller;

use App\Entity\ClubTeacher;
use App\Entity\Grade;
use App\Entity\GradeSession;
use App\Entity\GradeSessionCandidate;
use App\Entity\Member;

use App\Form\GradeType;

use App\Service\Access;
use App\Service\EmailSender;
use App\Service\FileGenerator;
use App\Service\ListData;
use App\Service\SearchMember;

use DateTime;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommonController
 * @package App\Controller
 */
#[Route('/grade', name:'grade-')]
class GradeController extends AbstractController
{
    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/', name:'list')]
    public function list(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('Grade-List'))
        {
            die();
        }

        $data['Sessions'] = $doctrine->getRepository(GradeSession::class)->findBy(array(), array('gradeSessionDate' => 'DESC'));

        return $this->render('Grade/list.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/ajouter-une-session', name:'sessionAdd')]
    public function sessionAdd(Access $access, ManagerRegistry $doctrine, Request $request): Response
    {
        if (!$access->check('Grade-SessionAdd'))
        {
            die();
        }

        $gradeSession = new GradeSession();

        $form = $this->createForm(GradeType::class, $gradeSession, array('formData' => array('Form' => 'Session', 'Action' => 'Add'), 'data_class' => GradeSession::class, 'action' => $this->generateUrl('grade-sessionAdd')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->persist($gradeSession);
            $entityManager->flush();

            return $this->redirectToRoute('grade-list');
        }

        return $this->render('/Grade/Modal/sessionsAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param GradeSession $gradeSession
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/modifier-une-session/{gradeSession<\d+>}', name:'sessionEdit')]
    public function sessionEdit(Access $access, GradeSession $gradeSession, ManagerRegistry $doctrine, Request $request): Response
    {
        if (!$access->check('Grade-SessionEdit'))
        {
            die();
        }

        $form = $this->createForm(GradeType::class, $gradeSession, array('formData' => array('Form' => 'Session', 'Action' => 'Edit'), 'data_class' => GradeSession::class, 'action' => $this->generateUrl('grade-sessionEdit', array('gradeSession' => $gradeSession->getGradeSessionId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        return $this->render('/Grade/Modal/sessionsEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param GradeSession $gradeSession
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param SearchMember $search
     * @param Session $session
     * @return Response
     */
    #[Route('/details-de-la-session/{gradeSession<\d+>}', name:'index')]
    public function index(Access $access, GradeSession $gradeSession, ManagerRegistry $doctrine, Request $request, SearchMember $search, Session $session): Response
    {
        if (!$access->check('Grade-Index') && !$session->has('Club'))
        {
            die();
        }

        $data['Candidates'] = array();
        $data['Session']    = $gradeSession;

        $data['Kagami'] = $doctrine->getRepository(Member::class)->getKagamiList();

        foreach ($gradeSession->getGradeSessionCandidates() as $candidate)
        {
            if (isset($data['Kagami']['Candidate'][$candidate->getGradeSessionCandidateRank()][$candidate->getGradeSessionCandidateMember()->getMemberId()]))
            {
                unset($data['Kagami']['Candidate'][$candidate->getGradeSessionCandidateRank()][$candidate->getGradeSessionCandidateMember()->getMemberId()]);
            }

            if ($session->has('Club'))
            {
                if ($candidate->getGradeSessionCandidateMember()->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId())
                {
                    continue;
                }

                if (($candidate->getGradeSessionCandidateRank() > 14 ) && ($gradeSession->getGradeSessionType() == 2))
                {
                    continue;
                }
            }

            if (is_null($candidate->getGradeSessionCandidatePaymentDate()) && ($candidate->getGradeSessionCandidateStatus() != 2) && $access->check('Grade-PaymentView'))
            {
                $data['Candidates']['Payment'][] = $candidate;
            }

            if (is_null($candidate->getGradeSessionCandidateStatus()) && $access->check('Grade-CandidatesAwaiting'))
            {
                $data['Candidates']['Awaiting'][] = $candidate;

                continue;
            }

            if ($candidate->getGradeSessionCandidateStatus() == 1)
            {
                if (($candidate->getGradeSessionCandidateResult() == 1) && $access->check('Grade-ValidatedSuccess'))
                {
                    $data['Candidates']['Validated']['Success'][] = $candidate;

                    continue;
                }

                if (($candidate->getGradeSessionCandidateResult() == 2) && $access->check('Grade-ValidatedFail'))
                {
                    $data['Candidates']['Validated']['Fail'][] = $candidate;

                    continue;
                }

                if (($candidate->getGradeSessionCandidateResult() == 3) && $access->check('Grade-ValidatedNoShow'))
                {
                    $data['Candidates']['Validated']['NoShow'][] = $candidate;

                    continue;
                }

                if ($access->check('Grade-ValidatedAwaiting'))
                {
                    $data['Candidates']['Validated']['Awaiting'][] = $candidate;
                }

                continue;
            }

            $data['Candidates']['Rejected'][] = $candidate;
        }

        if ($access->check('Grade-Search'))
        {
            $form = $this->createForm(GradeType::class, null, array('formData' => array('Form' => 'Search'), 'action' => $this->generateUrl('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()))));

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $data['Member'] = $search->getResults($form->get('CandidateId')->getData(), $gradeSession->getGradeSessionType());

                if (sizeof($data['Member']) == 0)
                {
                    $this->addFlash('warning', 'Aucun membre avec un grade entre 1er kyu et 3ème dan avec ce nom ou ce numéro de licence.' );
                }
            }
        }

        return $this->render('/Grade/index.html.twig', array('form' => $access->check('Grade-Search') ? $form->createView() : null, 'data' => $data));
    }

    /**
     * @param Access $access
     * @param GradeSession $gradeSession
     * @return Response
     */
    #[Route('/details-de-la-session/{gradeSession<\d+>}/critere-d-inscription', name:'sessionCriteria')]
    public function sessionCriteria(Access $access, GradeSession $gradeSession): Response
    {
        if (!$access->check('Grade-Criteria'))
        {
            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        return $this->render('/Grade/Modal/criteria.html.twig');
    }

    /**
     * @param Access $access
     * @param GradeSession $gradeSession
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @return Response
     */
    #[Route('/details-de-la-session/{gradeSession<\d+>}/ajouter-le-membre/{member<\d+>}', name:'candidateAdd')]
    public function candidateAdd(Access $access, GradeSession $gradeSession, ManagerRegistry $doctrine, Member $member, Request $request): Response
    {
        if (!$access->check('Grade-Search'))
        {
            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        if (!is_null($doctrine->getRepository(GradeSessionCandidate::class)->findOneBy(array('gradeSessionCandidateExam' => $gradeSession->getGradeSessionId(), 'gradeSessionCandidateMember' => $member->getMemberId()))))
        {
            $this->addFlash('warning', 'Ce membre est déjà inscrit à cette session');

            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        $candidate = new GradeSessionCandidate();

        $candidate->setGradeSessionCandidateDate(new DateTime());
        $candidate->setGradeSessionCandidateExam($gradeSession);
        $candidate->setGradeSessionCandidateMember($member);
        $candidate->setGradeSessionCandidateRank($member->getMemberLastGrade()->getGradeRank() % 2 == 0 ? $member->getMemberLastGrade()->getGradeRank() + 2 : $member->getMemberLastGrade()->getGradeRank() + 3);

        if ($gradeSession->getGradeSessionType() == 2)
        {
            $candidate->setGradeSessionCandidateResult(1);
            $candidate->setGradeSessionCandidatePaymentDate(new DateTime());
        }

        $form = $this->createForm(GradeType::class, $candidate, array('formData' => array('Form' => 'Application', 'Type' => $gradeSession->getGradeSessionType()), 'data_class' => GradeSessionCandidate::class, 'action' => $this->generateUrl('grade-candidateAdd', array('gradeSession' => $gradeSession->getGradeSessionId(), 'member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->persist($candidate);
            $entityManager->flush();

            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        return $this->render('/Grade/Modal/candidatesAdd.html.twig', array('form' => $form->createView(), 'gradeSession' => $gradeSession));
    }

    /**
     * @param Access $access
     * @param GradeSession $gradeSession
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @return Response
     */
    #[Route('/details-de-la-session/{gradeSession<\d+>}/date-de-paiement/{member<\d+>}', name:'candidatePaymentDate')]
    public function candidatePaymentDate(Access $access, GradeSession $gradeSession, ManagerRegistry $doctrine, Member $member, Request $request): Response
    {
        if (!$access->check('Grade-PaymentEdit'))
        {
            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        $candidate = $doctrine->getRepository(GradeSessionCandidate::class)->findOneBy(array('gradeSessionCandidateExam' => $gradeSession->getGradeSessionId(), 'gradeSessionCandidateMember' => $member->getMemberId()));

        $candidate->setGradeSessionCandidatePaymentDate(new DateTime());

        $form = $this->createForm(GradeType::class, $candidate, array('formData' => array('Form' => 'PaymentDate'), 'data_class' => GradeSessionCandidate::class, 'action' => $this->generateUrl('grade-candidatePaymentDate', array('gradeSession' => $gradeSession->getGradeSessionId(), 'member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        return $this->render('/Grade/Modal/paymentDate.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param EmailSender $emailSender
     * @param GradeSession $gradeSession
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @param int $action
     * @return Response
     * @throws TransportExceptionInterface
     */
    #[Route('/details-de-la-session/{gradeSession<\d+>}/details-du-membre/{member<\d+>}/{action<\d+>}/', name:'candidateDetails')]
    public function candidateDetails(Access $access, EmailSender $emailSender, GradeSession $gradeSession, ManagerRegistry $doctrine, Member $member, Request $request, int $action = 0): Response
    {
        if (!$access->check('Grade-CandidatesAwaitingAction'))
        {
            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        $candidate['Member'] = $member;

        $candidate['Session'] = $doctrine->getRepository(GradeSessionCandidate::class)->findOneBy(array('gradeSessionCandidateExam' => $gradeSession->getGradeSessionId(), 'gradeSessionCandidateMember' => $member->getMemberId()));

        $candidate['New'] = false;

        if (is_null($candidate['Session']))
        {
            $candidate['Session'] = new GradeSessionCandidate();

            $candidate['Session']->setGradeSessionCandidateMember($member);
            $candidate['Session']->setGradeSessionCandidateExam($gradeSession);
            $candidate['Session']->setGradeSessionCandidateDate(new DateTime());
            $candidate['Session']->setGradeSessionCandidatePaymentDate(new DateTime());
            $candidate['Session']->setGradeSessionCandidateResult(1);
            $candidate['Session']->setGradeSessionCandidateComment(null);
            $candidate['Session']->setGradeSessionCandidateRank($member->getMemberLastGrade()->getGradeRank() % 2 == 0 ? $member->getMemberLastGrade()->getGradeRank() + 2 : $member->getMemberLastGrade()->getGradeRank() + 3);

            $candidate['New'] = true;
        }

        if ($gradeSession->getGradeSessionType() == 1)
        {
            $candidate['OldSession'] = $doctrine->getRepository(GradeSessionCandidate::class)->findBy(array('gradeSessionCandidateMember' => $member->getMemberId()), array('gradeSessionCandidateDate' => 'DESC'));
        }
        else
        {
            $candidate['OldSession'] = $member->getMemberLastGradeExam();
        }

        if ($member->getMemberLastGrade()->getGradeRank() == 6)
        {
            $start = '1900-01-01';
        }
        elseif (($gradeSession->getGradeSessionType() == 1) && (sizeof($candidate['OldSession']) > 1))
        {
            $start = date('Y-m-d', strtotime('-1 month', strtotime($candidate['OldSession'][1]->getGradeSessionCandidateExam()->getGradeSessionDate()->format('Y-m-d'))));
        }
        else
        {
            $start = date('Y-m-d', strtotime('-1 month', strtotime($member->getMemberLastGrade()->getGradeDate()->format('Y-m-d'))));
        }

        $end = date('Y-m-d', strtotime('-1 month', strtotime($gradeSession->getGradeSessionDate()->format('Y-m-d'))));

        $candidate['TrainingActual'] = $doctrine->getRepository(Member::class)->getMemberAttendancesTotal($member->getMemberId(), $start, $end);

        $candidate['TrainingTotal'] = $doctrine->getRepository(Member::class)->getMemberAttendancesTotal($member->getMemberId());

        if ($access->check('Grade-GradeValidate'))
        {
            $formValidate = $this->createForm(GradeType::class, $candidate['Session'], array('formData' => array('Form' => 'Rank'), 'data_class' => GradeSessionCandidate::class, 'action' => $this->generateUrl('grade-candidateDetails', array('gradeSession' => $gradeSession->getGradeSessionId(), 'member' => $member->getMemberId(), 'action' => 1))));

            $formValidate->handleRequest($request);

            if ($action == 1 && $formValidate->isSubmitted() && $formValidate->isValid())
            {
                $candidate['Session']->setGradeSessionCandidateStatus(1);

                $entityManager = $doctrine->getManager();

                if ($gradeSession->getGradeSessionType() == 1)
                {
                    $emailSender->examApplicationValidated($member, $gradeSession);
                }

                if ($candidate['New'])
                {
                    $entityManager->persist($candidate['Session']);
                }

                $entityManager->flush();

                return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
            }

            $formReject = $this->createForm(GradeType::class, $candidate['Session'], array('formData' => array('Form' => 'Reject'), 'data_class' => GradeSessionCandidate::class, 'action' => $this->generateUrl('grade-candidateDetails', array('gradeSession' => $gradeSession->getGradeSessionId(), 'member' => $member->getMemberId(), 'action' => 2))));

            $formReject->handleRequest($request);

            if ($action == 2 && $formReject->isSubmitted() && $formReject->isValid())
            {
                $candidate['Session']->setGradeSessionCandidateStatus(2);

                $entityManager = $doctrine->getManager();

                if ($gradeSession->getGradeSessionType() == 1)
                {
                    $emailSender->examApplicationRejected($member, $candidate['Session']);
                }

                if ($candidate['New'])
                {
                    $entityManager->persist($candidate['Session']);
                }

                $entityManager->flush();

                return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
            }

            $formDelete = $this->createForm(GradeType::class, $candidate['Session'], array('formData' => array('Form' => 'Delete'), 'data_class' => GradeSessionCandidate::class, 'action' => $this->generateUrl('grade-candidateDetails', array('gradeSession' => $gradeSession->getGradeSessionId(), 'member' => $member->getMemberId(), 'action' => 3))));

            $formDelete->handleRequest($request);

            if ($action == 3 && $formDelete->isSubmitted() && $formDelete->isValid())
            {
                $entityManager = $doctrine->getManager();

                $entityManager->remove($candidate['Session']);
                $entityManager->flush();

                return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
            }

            $formCancel = $this->createForm(GradeType::class, $candidate['Session'], array('formData' => array('Form' => 'Cancel'), 'data_class' => GradeSessionCandidate::class, 'action' => $this->generateUrl('grade-candidateDetails', array('gradeSession' => $gradeSession->getGradeSessionId(), 'member' => $member->getMemberId(), 'action' => 4))));

            $formCancel->handleRequest($request);

            if ($action == 4 && $formCancel->isSubmitted() && $formCancel->isValid())
            {
                $entityManager = $doctrine->getManager();

                $member->setMemberLastKagami(1);

                $entityManager->remove($candidate['Session']);
                $entityManager->flush();

                return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
            }

            return $this->render('Grade/Modal/candidatesDetails.html.twig', array('formValidate' => $formValidate->createView(), 'formReject' => $formReject->createView(), 'formDelete' => $formDelete->createView(), 'formCancel' => $formCancel->createView(), 'candidate' => $candidate, 'gradeSession' => $gradeSession));
        }

        return $this->render('Grade/Modal/candidatesDetails.html.twig', array('candidate' => $candidate, 'gradeSession' => $gradeSession));
    }

    /**
     * @param Access $access
     * @param EmailSender $emailSender
     * @param GradeSession $gradeSession
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    #[Route('/details-de-la-session/{gradeSession<\d+>}/email-horaire-des-sessions', name:'candidateEmail')]
    public function candidateEmail(Access $access, EmailSender $emailSender, GradeSession $gradeSession, Request $request): Response
    {
        if (!$access->check('Grade-CandidateEmail'))
        {
            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        $form = $this->createForm(GradeType::class, null, array('formData' => array('Form' => 'Schedule'), 'action' => $this->generateUrl('grade-candidateEmail', array('gradeSession' => $gradeSession->getGradeSessionId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data['Shodan']['Schedule'] = $form->get('GradeSessionShodan')->getData();
            $data['Nidan']['Schedule']  = $form->get('GradeSessionNidan')->getData();
            $data['Sandan']['Schedule'] = $form->get('GradeSessionSandan')->getData();
            $data['Yondan']['Schedule'] = $form->get('GradeSessionYondan')->getData();

            $emailSender->examSchedule($data, $gradeSession);

            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        return $this->render('Grade/Modal/candidatesSchedule.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param FileGenerator $fileGenerator
     * @param GradeSession $gradeSession
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/details-de-la-session/{gradeSession<\d+>}/formulaire-de-cotation', name:'candidateForms')]
    public function candidateForms(Access $access, FileGenerator $fileGenerator, GradeSession $gradeSession, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('Grade-CandidateForms'))
        {
            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        $candidates = $doctrine->getRepository(GradeSessionCandidate::class)->getSessionForms($gradeSession->getGradeSessionId());

        $members = array();

        foreach ($candidates as $candidate)
        {
            $candidate['Sessions'] = $doctrine->getRepository(GradeSessionCandidate::class)->findBy(array('gradeSessionCandidateMember' => $candidate['Id']), array('gradeSessionCandidateDate' => 'DESC'));

            if ($candidate['ActualGrade'] == 6)
            {
                $start = '1900-01-01';
            }
            elseif (sizeof($candidate['Sessions']) > 1)
            {
                $start = date('Y-m-d', strtotime('-1 month', strtotime($candidate['Sessions'][1]->getGradeSessionCandidateExam()->getGradeSessionDate()->format('Y-m-d'))));
            }
            else
            {
                $start = date('Y-m-d', strtotime('-1 month', strtotime($candidate['ActualGradeDate'])));
            }

            $end = date('Y-m-d', strtotime('-1 month', strtotime($gradeSession->getGradeSessionDate()->format('Y-m-d'))));

            $candidate['TrainingActual'] = $doctrine->getRepository(Member::class)->getMemberAttendancesTotal($candidate['Id'], $start, $end);

            $candidate['Teachers'] = $doctrine->getRepository(ClubTeacher::class)->getDojoCho($candidate['ClubId']);

            $members[] = $candidate;
        }

        $user = $this->getUser()->getMember()->getMemberFirstname() . ' ' . $this->getUser()->getMember()->getMemberName();

        $notationForm = $this->renderView('Grade/Print/notationForm.html.twig', array('members' => $members, 'gradeSession' => $gradeSession, 'user' => $user));

        $pdf = $fileGenerator->pdfGenerator($this->getParameter('kernel.project_dir').'/private/formulaires.pdf', $notationForm, false);

        $response = new BinaryFileResponse($pdf);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response->deleteFileAfterSend();
    }

    /**
     * @param Access $access
     * @param GradeSession $gradeSession
     * @return Response
     */
    #[Route('/details-de-la-session/{gradeSession<\d+>}/liste-csv', name:'csvList')]
    public function csvList(Access $access, GradeSession $gradeSession): Response
    {
        if (!$access->check('Grade-AikikaiList'))
        {
            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        $candidates = $gradeSession->getGradeSessionCandidates();

        $file = fopen('List.csv', 'w');

        fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($file, ['Licence', 'Prénom', 'Nom', 'Sexe', 'Début de Pratique', 'Grade Présenté', 'Date de naissance', 'Code Postal', 'Localité', 'Id Aïkikaï', 'Date enregistrement', 'Grade AFA Actuel', 'Date de passage', 'Grade Aïkikaï Actuel', 'Date de passage'], ";");

        $listData = new ListData();

        foreach ($candidates as $candidate)
        {
            if ($candidate->getGradeSessionCandidateStatus() != 1)
            {
                continue;
            }

            $member = $candidate->getGradeSessionCandidateMember();

            $entry['Licence']          = $member->getMemberId();
            $entry['Firstname']        = $member->getMemberFirstname();
            $entry['Name']             = $member->getMemberName();
            $entry['Sex']              = $member->getMemberSex(true);
            $entry['StartPractice']    = $member->getMemberStartPractice()?->format('d/m/Y');
            $entry['Grade']            = $listData->getGrade($candidate->getGradeSessionCandidateRank());
            $entry['Birthday']         = $member->getMemberBirthday()?->format('d/m/Y');
            $entry['Zip']              = $member->getMemberZip();
            $entry['City']             = $member->getMemberCity();
            $entry['AikikaiId']        = $member->getMemberAikikaiId();
            $entry['AikikaiDate']      = $member->getMemberAikikaiDate()?->format('d/m/Y');
            $entry['AFAGrade']         = is_null($member->getMemberLastGradeAFA()) ? null : $listData->getGrade($member->getMemberLastGradeAFA()->getGradeRank());
            $entry['AFAGradeDate']     = $member->getMemberLastGradeAFA()?->getGradeDate()?->format('d/m/Y');
            $entry['AikikaiGrade']     = is_null($member->getMemberLastGradeAikikai()) ? null : $listData->getGrade($member->getMemberLastGradeAikikai()->getGradeRank());
            $entry['AikikaiGradeDate'] = $member->getMemberLastGradeAikikai()?->getGradeDate()?->format('d/m/Y');

            fputcsv($file, $entry, ";");
        }

        fclose($file);

        $stream = new Stream('List.csv');

        $response = new BinaryFileResponse($stream);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export.csv');

        return $response->deleteFileAfterSend();
    }

    /**
     * @param Access $access
     * @param GradeSession $gradeSession
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @return Response
     */
    #[Route('/details-de-la-session/{gradeSession<\d+>}/resultats-du-membre/{member<\d+>}', name:'gradeValidate')]
    public function gradeValidate(Access $access, GradeSession $gradeSession, ManagerRegistry $doctrine, Member $member, Request $request): Response
    {
        if (!$access->check('Grade-GradeValidate'))
        {
            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        $candidate = $doctrine->getRepository(GradeSessionCandidate::class)->findOneBy(array('gradeSessionCandidateExam' => $gradeSession->getGradeSessionId(), 'gradeSessionCandidateMember' => $member->getMemberId()));

        if ($gradeSession->getGradeSessionType() == 1)
        {
            $form = $this->createForm(GradeType::class, $candidate, array('formData' => array('Form' => 'GradeValidate', 'Action' => 'Validate'), 'data_class' => GradeSessionCandidate::class, 'action' => $this->generateUrl('grade-gradeValidate', array('gradeSession' => $gradeSession->getGradeSessionId(), 'member' => $member->getMemberId()))));
        }
        else
        {
            $candidate->setGradeSessionCandidateStatus(null);

            $form = $this->createForm(GradeType::class, $candidate, array('formData' => array('Form' => 'GradeValidate', 'Action' => 'Unvalidate'), 'data_class' => GradeSessionCandidate::class, 'action' => $this->generateUrl('grade-gradeValidate', array('gradeSession' => $gradeSession->getGradeSessionId(), 'member' => $member->getMemberId()))));
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        return $this->render('/Grade/Modal/gradeValidate.html.twig', array('form' => $form->createView(), 'gradeSession' => $gradeSession, 'candidate' => $candidate));
    }

    /**
     * @param Access $access
     * @param GradeSession $gradeSession
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/details-de-la-session/{gradeSession<\d+>}/publication-des-resultats', name:'assignment')]
    public function assignment(Access $access, GradeSession $gradeSession, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('Grade-Assignment'))
        {
            return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
        }

        $list = $gradeSession->getGradeSessionCandidates();

        foreach ($list as $candidate)
        {
            if ($candidate->getGradeSessionCandidateResult() == 3)
            {
                continue;
            }

            if (($candidate->getGradeSessionCandidateStatus() == 1) && !is_null($candidate->getGradeSessionCandidateResult()))
            {
                $club = $candidate->getCandidateMemberClub();

                $member = $candidate->getGradeSessionCandidateMember();

                $entityManager = $doctrine->getManager();

                if ($candidate->getGradeSessionCandidateResult() == 2)
                {
                    $result = 4;
                }
                elseif ($candidate->getGradeSessionCandidateResult() == 1)
                {
                    $result = $candidate->getGradeSessionCandidateExam()->getGradeSessionType() == 1 ? 1 : 2;
                }

                if (($candidate->getGradeSessionCandidateRank() % 2 == 0) && ($candidate->getGradeSessionCandidateResult() == 1))
                {
                    $grade = $doctrine->getRepository(Grade::class)->findOneBy(array('gradeMember' => $candidate->getCandidateMemberId(), 'gradeRank' => $candidate->getGradeSessionCandidateRank() - 1));

                    if (is_null($grade))
                    {
                        $grade = new Grade();

                        $grade->setGradeClub($club);
                        $grade->setGradeMember($member);
                        $grade->setGradeStatus($result);
                        $grade->setGradeSession($candidate);
                        $grade->setGradeDate($candidate->getGradeSessionCandidateExam()->getGradeSessionDate());
                        $grade->setGradeRank($candidate->getGradeSessionCandidateRank() - 1);

                        $entityManager->persist($grade);
                    }
                    elseif (is_null($grade->getGradeSession()) || $grade->getGradeSession() === $candidate->getGradeSessionCandidateExam())
                    {
                        $grade->setGradeStatus($result);
                        $grade->setGradeSession($candidate);
                        $grade->setGradeDate($candidate->getGradeSessionCandidateExam()->getGradeSessionDate());
                    }
                }

                if ($candidate->getGradeSessionCandidateMember()->getMemberLastGrade()->getGradeRank() < $candidate->getGradeSessionCandidateRank())
                {
                    $grade = new Grade();

                    $grade->setGradeClub($club);
                    $grade->setGradeMember($member);
                    $grade->setGradeStatus($result);
                    $grade->setGradeSession($candidate);
                    $grade->setGradeRank($candidate->getGradeSessionCandidateRank());

                    if (($candidate->getGradeSessionCandidateResult() == 2) || ($candidate->getGradeSessionCandidateRank() % 2 != 0))
                    {
                        $grade->setGradeDate($candidate->getGradeSessionCandidateExam()->getGradeSessionDate());
                    }

                    $entityManager->persist($grade);
                }
                else
                {
                    $grade = $doctrine->getRepository(Grade::class)->findOneBy(array('gradeMember' => $candidate->getCandidateMemberId(), 'gradeRank' => $candidate->getGradeSessionCandidateRank()));

                    if (is_null($grade->getGradeSession()) || $grade->getGradeSession() === $candidate->getGradeSessionCandidateExam())
                    {
                        $grade->setGradeStatus($result);
                        $grade->setGradeSession($candidate);

                        $candidate->getGradeSessionCandidateRank() % 2 == 0 ?: $grade->setGradeDate($candidate->getGradeSessionCandidateExam()->getGradeSessionDate());
                    }
                }

                $entityManager->flush();
            }
        }

        $this->addFlash('success', 'Tous les grades ont été attribués aux candidats' );

        return $this->redirectToRoute('grade-index', array('gradeSession' => $gradeSession->getGradeSessionId()));
    }
}
