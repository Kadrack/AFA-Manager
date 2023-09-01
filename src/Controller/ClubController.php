<?php
// src/Controller/ClubController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubDojo;
use App\Entity\ClubClass;
use App\Entity\ClubHistory;
use App\Entity\ClubManager;
use App\Entity\ClubTeacher;
use App\Entity\Grade;
use App\Entity\Lesson;
use App\Entity\LessonAttendance;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\NewsletterSubscription;
use App\Entity\TrainingSessionAttendance;
use App\Entity\User;

use App\Form\ClubType;

use App\Service\Access;
use App\Service\EmailSender;
use App\Service\FileGenerator;
use App\Service\PhotoUploader;

use DateInterval;
use DateTime;

use Doctrine\Persistence\ManagerRegistry;

use Exception;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

use ZipArchive;

/**
 * Class ClubController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
#[Route('/club', name:'club-')]
class ClubController extends AbstractController
{
    /**
     * @param Access $access
     * @param Session $session
     * @return Response
     */
    #[Route('/', name:'list')]
    public function list(Access $access, Session $session): Response
    {
        if (!$access->check('Club-ListOpen')  && !$access->check('Club-ListClose'))
        {
            if ($session->has('Club'))
            {
                return $this->redirectToRoute('club-index', array('club' => $session->get('Club')->getClubId()));
            }
            elseif (!$session->has('Id') && !is_null($this->getUser()->getUserMember()))
            {
                return $this->redirectToRoute('club-index', array('club' => $this->getUser()->getUserMember()->getMemberActualClub()->getClubId()));
            }
            else
            {
                return $this->redirectToRoute('common-index');
            }
        }

        return $this->render('Club/list.html.twig');
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-club-ouvert', name:'listOpen')]
    public function listOpen(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('Club-ListOpen'))
        {
            die();
        }

        $data['List'] = $doctrine->getRepository(Club::class)->getClubList(null, null, true);
        $data['Open'] = true;

        return $this->render('Club/Load/listOpenClose.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-club-ferme', name:'listClose')]
    public function listClose(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('Club-ListClose'))
        {
            die();
        }

        $data['List'] = $doctrine->getRepository(Club::class)->getClubList(null, null, false);
        $data['Open'] = false;

        return $this->render('Club/Load/listOpenClose.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/ajouter-un-club', name:'clubAdd')]
    public function clubAdd(Access $access, ManagerRegistry $doctrine, Request $request): Response
    {
        if (!$access->check('Club-ClubAdd'))
        {
            die();
        }

        $club = new Club();

        $form = $this->createForm(ClubType::class, $club, array('formData' => array('Form' => 'Club', 'Action' => 'Add'), 'data_class' => Club::class, 'action' => $this->generateUrl('club-clubAdd')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if (is_null($doctrine->getRepository(Club::class)->findOneBy(array('club_id' => $club->getClubId()))))
            {
                $history = new ClubHistory();

                $history->setClubHistory($club);
                $history->setClubHistoryStatus(2);
                $history->setClubHistoryUpdate(new DateTime());

                $entityManager = $doctrine->getManager();

                $entityManager->persist($club);
                $entityManager->persist($history);
                $entityManager->flush();
            }
            else
            {
                $this->addFlash('danger', 'Ce numéro de club existe déjà');
            }

            return $this->redirectToRoute('club-list');
        }

        return $this->render('Club/Modal/clubAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}', name:'index')]
    public function index(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): Response
    {
        if (!$access->check('Club-Index'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        if ($session->has('activeClubTab') && ($session->get('activeClubTab') == 'attendanceTab'))
        {
            if ($request->query->has('season'))
            {
                $session->set('season', intval($request->query->get('season')));
            }
            else
            {
                $session->remove('season');
            }
        }

        $data['Club'] = $club;

        $data['ActiveMemberCount'] = $doctrine->getRepository(MemberLicence::class)->getClubActiveMemberCount($club);

        return $this->render('Club/index.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/onglet-presence', name:'attendanceTab')]
    public function attendanceTab(Access $access, Club $club, ManagerRegistry $doctrine, Session $session): Response
    {
        if (!$access->check('Club-AttendanceTab'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeClubTab', 'attendanceTab');

        $today = new DateTime();

        if ($session->has('season') && ($session->get('season') >= 2022) && ($session->get('season') < intval($today->format('Y'))))
        {
            $data['Season'] = $session->get('season');
        }
        else
        {
            if (intval($today->format('n')) < 8)
            {
                $data['Season'] = intval($today->format('Y')) - 1;
            }
            else
            {
                $data['Season'] = intval($today->format('Y'));
            }
        }

        $data['SeasonList'] = array();

        for ($i = intval($today->format('Y')) < 8 ? intval($today->format('Y')) - 1 : intval($today->format('Y')); $i >= 2022; $i--)
        {
            $data['SeasonList'][] = $i;
        }

        $data['Club']    = $club;
        $data['Lesson']  = $doctrine->getRepository(Lesson::class)->getLesson($data['Club'], $data['Season']);
        $data['Summary'] = $doctrine->getRepository(Lesson::class)->getSummary($data['Club'], $data['Season']);

        return $this->render('Club/Tab/attendance.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/onglet-adulte', name:'adultTab')]
    public function adultTab(Access $access, Club $club, ManagerRegistry $doctrine, Session $session): Response
    {
        if (!$access->check('Club-AdultTab'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeClubTab', 'adultTab');

        $data['Club']             = $club;
        $data['ActiveMemberList'] = $doctrine->getRepository(Member::class)->getClubActiveMemberList($club, true);
        $data['AttendanceTotal']  = $doctrine->getRepository(TrainingSessionAttendance::class)->getClubAttendanceTotalHour($club);

        foreach ($data['AttendanceTotal'] as $member)
        {
            $total[$member['Id']] = $member['Total'];
        }

        $data['AttendanceTotal'] = $total;

        return $this->render('Club/Tab/memberList.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/onglet-enfant', name:'childTab')]
    public function childTab(Access $access, Club $club, ManagerRegistry $doctrine, Session $session): Response
    {
        if (!$access->check('Club-ChildTab'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeClubTab', 'childTab');

        $data['Club']             = $club;
        $data['ActiveMemberList'] = $doctrine->getRepository(Member::class)->getClubActiveMemberList($club, false);
        $data['AttendanceTotal']  = $doctrine->getRepository(TrainingSessionAttendance::class)->getClubAttendanceTotalHour($club);

        foreach ($data['AttendanceTotal'] as $member)
        {
            $total[$member['Id']] = $member['Total'];
        }

        $data['AttendanceTotal'] = $total;

        return $this->render('Club/Tab/memberList.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param EmailSender $emailSender
     * @param Request $request
     * @param Session $session
     * @return Response
     * @throws TransportExceptionInterface
     */
    #[Route('/{club<\d+>}/onglet-email', name:'emailTab')]
    public function emailTab(Access $access, Club $club, EmailSender $emailSender, Request $request, Session $session): Response
    {
        if (!$access->check('Club-EmailTab'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeClubTab', 'emailTab');

        $data['List'] = $emailSender->getClubMemberMailingList();

        $form = $this->createForm(ClubType::class, null, array('formData' => array('Form' => 'Mailing', 'Data' => $data), 'action' => $this->generateUrl('club-emailTab', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $emailSender->setSubject($form['Subject']->getData());
            $emailSender->setContent($form['Text']->getData());

            is_null($form['Attachment']->getData()) ?: $emailSender->setAttachment($form['Attachment']->getData());

            $emailSender->toClubMember($club, $form['To']->getData());

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Tab/email.html.twig', array('form' => $form->createView(), 'data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/onglet-dojo', name:'dojoTab')]
    public function dojoTab(Access $access, Club $club, Session $session): Response
    {
        if (!$access->check('Club-DojoTab'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeClubTab', 'dojoTab');

        $data['Club'] = $club;

        return $this->render('Club/Tab/dojo.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/onglet-secretariat', name:'secretariatTab')]
    public function secretariatTab(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): Response
    {
        if (!$access->check('Club-SecretariatTab'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeClubTab', 'secretariatTab');

        $data['Club']         = $club;
        $data['Stamp']        = $doctrine->getRepository(Member::class)->getOnGoingStampMember($club);
        $data['Payment']      = $doctrine->getRepository(MemberLicence::class)->getPaymentOnGoing($club);
        $data['ToRenew']      = $doctrine->getRepository(Member::class)->getMemberToRenew($club);
        $data['Inactive']     = $doctrine->getRepository(Member::class)->getRecentExpired($club);
        $data['ExamToPay']    = $doctrine->getRepository(Member::class)->getUnpayedSession($club);

        if ($request->query->has('date'))
        {
            $date = DateTime::createFromFormat("Ymd", (string)$request->query->get('date'));
        }
        else
        {
            $date = null;
        }

        $data['Subscription'] = $doctrine->getRepository(Member::class)->getClubActiveMemberList($club, null, $date);

        $ids = array();

        foreach ($data['Subscription'] as $member)
        {
            $ids[] = $member->getMemberId();
        }

        $lastLesson = $doctrine->getRepository(LessonAttendance::class)->getLastLesson($ids);

        foreach ($lastLesson as $id)
        {
            $data['LastLesson'][$id['Id']] = $id['Last'];
        }

        return $this->render('Club/Tab/secretariat.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/onglet-gestion', name:'managementTab')]
    public function managementTab(Access $access, Club $club, Session $session): Response
    {
        if (!$access->check('Club-ManagementTab'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeClubTab', 'managementTab');

        $data['Club'] = $club;

        return $this->render('Club/Tab/management.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param PhotoUploader $photoUploader
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     * @throws Exception
     */
    #[Route('/{club<\d+>}/ajouter-un-membre', name:'membersAdd')]
    public function membersAdd(Access $access, Club $club, ManagerRegistry $doctrine, PhotoUploader $photoUploader, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-MemberAdd'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $member = new Member();

        $form = $this->createForm(ClubType::class, $member, array('formData' => array('Form' => 'Member', 'Action' => 'Add'), 'data_class' => Member::class, 'action' => $this->generateUrl('club-membersAdd', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member->setMemberStartPractice($form->get('MemberLicenceMedicalCertificate')->getData());
            $member->setMemberPhoto(is_null($form['MemberPhoto']->getData()) ? null : $photoUploader->upload($form['MemberPhoto']->getData()));

            if ($member->getMemberBirthday() > new DateTime('-14 year today'))
            {
                $member->setMemberSubscriptionList(2);
            }
            else
            {
                $member->setMemberSubscriptionList(1);
            }

            $licence = new MemberLicence();

            $licence->setMemberLicenceClub($club);
            $licence->setMemberLicenceUpdate(new DateTime());
            $licence->setMemberLicenceDeadline(new DateTime('+1 year '.$form->get('MemberLicenceMedicalCertificate')->getData()->format('Y-m-d')));

            $member->addMemberLicences($licence);

            $grade = new Grade();

            $grade->setGradeClub($club);
            $grade->setGradeRank($form->get('GradeRank')->getData());
            $grade->setGradeDate($member->getMemberStartPractice());

            if ($grade->getGradeRank() < 7)
            {
                $grade->setGradeStatus(1);
            }
            else
            {
                $grade->setGradeStatus(3);
            }

            $member->addMemberGrades($grade);

            $entityManager = $doctrine->getManager();

            if (!is_null($form['MemberEmail']->getData()))
            {
                if (is_null($doctrine->getRepository(NewsletterSubscription::class)->findOneBy(array('newsletter_subscription_email' => $member->getMemberEmail()))))
                {
                    $subscription = new NewsletterSubscription();

                    $subscription->setNewsletterSubscriptionEmail($member->getMemberEmail());

                    $entityManager->persist($subscription);
                }
            }

            $entityManager->persist($member);
            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('/Club/Modal/memberAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Member $member
     * @param int $source
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/membre/{member<\d+>}/details/{source<\d+>}', name:'memberDetail')]
    public function memberDetail(Access $access, Club $club, Member $member, int $source): RedirectResponse|Response
    {
        if (!$access->check('Club-MemberDetail') || $member->getMemberActualClub() !== $club)
        {
            die();
        }

        $data['Member'] = $member;
        $data['Source'] = $source;

        return $this->render('Club/Modal/memberDetail.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/gestion-des-timbres', name:'printStamp')]
    public function printStamp(Access $access, Club $club, Session $session): Response
    {
        if (!$access->check('Club-PrintStamp'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $data['Club'] = $club;

        return $this->render('Club/Modal/printStamp.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/imprimer-les-timbres', name:'printStampView')]
    public function printStampView(Access $access, Club $club, ManagerRegistry $doctrine, Session $session): Response
    {
        if (!$access->check('Club-PrintStamp'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $data['Member'] = $doctrine->getRepository(Member::class)->getOnGoingStampMember($club);

        return $this->render('Member/Print/stamps.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/valider-impression-des-timbres', name:'printStampValidate')]
    public function printStampValidate(Access $access, Club $club, ManagerRegistry $doctrine, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-PrintStamp'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $list = $doctrine->getRepository(MemberLicence::class)->getOnGoingStampLicence($club);

        $entityManager = $doctrine->getManager();

        foreach ($list as $licence)
        {
            $licence->setMemberLicencePrintoutDone(new DateTime());

            $entityManager->flush();
        }

        return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/validation-paiement', name:'paymentAdd')]
    public function paymentAdd(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): Response
    {
        if (!$access->check('Club-PaymentAdd'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(ClubType::class, null, array('formData' => array('Form' => 'Payment', 'Action' => 'Add'), 'action' => $this->generateUrl('club-paymentAdd', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $members = $doctrine->getRepository(Member::class)->findBy(['member_id' => explode(',', $form->get('LicenceNumber')->getData())]);

            $entityManager = $doctrine->getManager();

            foreach ($members as $member)
            {
                if ($member->getMemberActualClub() !== $club)
                {
                    continue;
                }

                if (is_null($member->getMemberLastLicence()->getMemberLicencePaymentDate()) && ($member->getMemberLastLicence()->getMemberLicenceDeadline() > new DateTime()))
                {
                    $member->getMemberLastLicence()->setMemberLicencePaymentDate($form->get('PaymentDate')->getData());
                    $member->getMemberLastLicence()->setMemberLicencePrintoutCreation(new DateTime());
                    $member->getMemberLastLicence()->setMemberLicencePaymentUpdate(new DateTime());
                }

                $entityManager->flush();
            }

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/paymentAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/formulaires-renouvellement', name:'formRenew')]
    public function formRenew(Access $access, Club $club, Session $session): Response
    {
        if (!$access->check('Club-FormRenew'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        return $this->render('Club/Modal/formRenew.html.twig', array('club' => $club));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param FileGenerator $fileGenerator
     * @param ManagerRegistry $doctrine
     * @param Session $session
     * @param int $month
     * @return BinaryFileResponse|Response
     */
    #[Route('/{club<\d+>}/telechargement-formulaires-renouvellement/{month<\d+>}', name:'formRenewDownload')]
    public function formRenewDownload(Access $access, Club $club, FileGenerator $fileGenerator, ManagerRegistry $doctrine, Session $session, int $month): BinaryFileResponse|Response
    {
        if (!$access->check('Club-FormDownload'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $zip = new ZipArchive();

        $zipname = $this->getParameter('kernel.project_dir').'/private/licences-club-'.$club->getClubId().'-mois-'.$month.'.zip';

        $zip->open($zipname, ZipArchive::CREATE);

        $today = new DateTime();

        if (intval($today->format('n')) <= $month)
        {
            $year = intval($today->format('Y'));
        }
        else
        {
            $year = intval($today->format('Y')) + 1;
        }

        $fileList = array();

        $start = date('Y-m-d', mktime(0, 0, 0, $month, 1, $year));
        $end   = date('Y-m-d', mktime(0, 0, 0, $month + 1, 0, $year));

        $members = $doctrine->getRepository(Member::class)->getClubRenewForms($club, $start, $end);

        if (sizeof($members) > 0)
        {
            foreach ($members as $member)
            {
                $data['Member'] = $member;

                $licenceForm = $this->renderView('Member/Print/licenceForm.html.twig', array('data' => $data));

                $filename = str_replace(' ', '', $member->getMemberId().'-'.strtolower($member->getMemberName()).'.pdf');

                $pdf = $fileGenerator->pdfGenerator($this->getParameter('kernel.project_dir').'/private/' . $filename, $licenceForm);

                $fileList[] = $pdf;

                $zip->addFile($pdf, $filename);
            }
        }

        $zip->close();

        if (sizeof($members) > 0)
        {
            foreach ($fileList as $file)
            {
                unlink($file);
            }

            $response = new BinaryFileResponse($zipname);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

            return $response->deleteFileAfterSend();
        }

        return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/ajouter-un-professeur', name:'teachersAdd')]
    public function teachersAdd(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-TeacherAdd'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $teacher = new ClubTeacher();

        $form = $this->createForm(ClubType::class, $teacher, array('formData' => array('Form' => 'Teacher', 'Action' => 'Add'), 'data_class' => ClubTeacher::class, 'action' => $this->generateUrl('club-teachersAdd', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $teacher->setClubTeacher($club);

            if (!is_null($form->get('ClubTeacherMember')->getData()))
            {
                $member = $doctrine->getRepository(Member::class)->findOneBy(['member_id' => $form->get('ClubTeacherMember')->getData()]);

                if ($member != null)
                {
                    $teacher->setClubTeacherMember($member);
                }
            }

            $entityManager = $doctrine->getManager();

            $entityManager->persist($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/teacherAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ClubTeacher $teacher
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/professeurs/{teacher<\d+>}/modifier', name:'teachersEdit')]
    public function teachersEdit(Access $access, Club $club, ClubTeacher $teacher, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-TeacherEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        if ($teacher->getClubTeacher() !== $club)
        {
            die();
        }

        $formDelete = $this->createForm(ClubType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('club-teachersEdit', array('club' => $club->getClubId(), 'teacher' => $teacher->getClubTeacherId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($teacher);
            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        $formEdit = $this->createForm(ClubType::class, $teacher, array('formData' => array('Form' => 'Teacher', 'Action' => 'Edit', 'IsMember' => !is_null($teacher->getClubTeacherMember())), 'data_class' => ClubTeacher::class, 'action' => $this->generateUrl('club-teachersEdit', array('club' => $club->getClubId(), 'teacher' => $teacher->getClubTeacherId()))));

        is_null($teacher->getClubTeacherMember()) ?: $formEdit->get('ClubTeacherMember')->setData($teacher->getClubTeacherMember()->getMemberId());

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            if (!is_null($formEdit->get('ClubTeacherMember')->getData()))
            {
                $member = $doctrine->getRepository(Member::class)->findOneBy(['member_id' => $formEdit->get('ClubTeacherMember')->getData()]);

                if ($member != null)
                {
                    $teacher->setClubTeacherMember($member);
                }
            }

            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/teacherEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView(), 'member' => is_null($teacher->getClubTeacherMember())));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/ajouter-un-dojo', name:'dojosAdd')]
    public function dojosAdd(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-DojoAdd'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $dojo = new ClubDojo();

        $form = $this->createForm(ClubType::class, $dojo, array('formData' => array('Form' => 'Dojo', 'Action' => 'Add'), 'data_class' => ClubDojo::class, 'action' => $this->generateUrl('club-dojosAdd', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $dojo->setClubDojoClub($club);

            $entityManager = $doctrine->getManager();

            $entityManager->persist($dojo);
            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/dojoAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ClubDojo $dojo
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/dojos/{dojo<\d+>}/modifier', name:'dojosEdit')]
    public function dojosEdit(Access $access, Club $club, ClubDojo $dojo, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-DojoEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        if ($dojo->getClubDojoClub() !== $club)
        {
            die();
        }

        $formDelete = $this->createForm(ClubType::class, $dojo, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('club-dojosEdit', array('club' => $club->getClubId(), 'dojo' => $dojo->getClubDojoId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($dojo);
            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        $formEdit = $this->createForm(ClubType::class, $dojo, array('formData' => array('Form' => 'Dojo', 'Action' => 'Edit'), 'data_class' => ClubDojo::class, 'action' => $this->generateUrl('club-dojosEdit', array('club' => $club->getClubId(), 'dojo' => $dojo->getClubDojoId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/dojoEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/ajouter-un-horaire-de-cours', name:'classAdd')]
    public function classAdd(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-ClassAdd'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $class = new ClubClass();

        $form = $this->createForm(ClubType::class, $class, array('formData' => array('Form' => 'Class', 'Action' => 'Add', 'Choices' => $club->getClubDojos()), 'data_class' => ClubClass::class, 'action' => $this->generateUrl('club-classAdd', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $class->setClubClassClub($club);

            $entityManager = $doctrine->getManager();

            $entityManager->persist($class);
            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/classAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ClubClass $class
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/cours/{class<\d+>}/modifier', name:'classEdit')]
    public function classEdit(Access $access, Club $club, ClubClass $class, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-ClassEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        if ($class->getClubClassClub() !== $club)
        {
            die();
        }

        $formDelete = $this->createForm(ClubType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('club-classEdit', array('club' => $club->getClubId(), 'class' => $class->getClubClassId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($class);
            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        $formEdit = $this->createForm(ClubType::class, $class, array('formData' => array('Form' => 'Class', 'Action' => 'Edit', 'Choices' => $club->getClubDojos()), 'data_class' => ClubClass::class, 'action' => $this->generateUrl('club-classEdit', array('club' => $club->getClubId(), 'class' => $class->getClubClassId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/classEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/modifier-historique', name:'historyEdit')]
    public function historyEdit(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-HistoryEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(ClubType::class, null, array('formData' => array('Form' => 'History'), 'action' => $this->generateUrl('club-historyEdit', array('club' => $club->getClubId()))));

        $form->get('CreationDate')->setData($club->getClubCreation());

        if ($club->getClubHistories()[0]->getClubHistoryStatus() == 3)
        {
            $form->get('MembershipDate')->setData($club->getClubHistories()[1]->getClubHistoryUpdate());
        }
        else if ($club->getClubHistories()[0]->getClubHistoryStatus() == 1)
        {
            $form->get('MembershipDate')->setData($club->getClubHistories()[0]->getClubHistoryUpdate());
        }

        if ($club->getClubHistories()[0]->getClubHistoryStatus() == 3)
        {
            $form->get('RetireDate')->setData($club->getClubHistories()[0]->getClubHistoryUpdate());
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $club->setClubCreation($form->get('CreationDate')->getData());

            if (!is_null($form->get('RetireDate')->getData()))
            {
                if (($club->getClubHistories()[0]->getClubHistoryStatus() == 1) || ($club->getClubHistories()[0]->getClubHistoryStatus() == 2))
                {
                    $history = new ClubHistory();

                    $history->setClubHistory($club);
                    $history->setClubHistoryStatus(3);
                    $history->setClubHistoryUpdate($form->get('RetireDate')->getData());

                    $entityManager->persist($history);
                }
                else if ($club->getClubHistories()[0]->getClubHistoryStatus() == 3)
                {
                    $club->getClubHistories()[0]->setClubHistoryUpdate($form->get('RetireDate')->getData());
                }
            }

            $entityManager->flush();

            if (!is_null($form->get('MembershipDate')->getData()))
            {
                if ($club->getClubHistories()[0]->getClubHistoryStatus() == 1)
                {
                    $club->getClubHistories()[0]->setClubHistoryUpdate($form->get('MembershipDate')->getData());
                }
                else if ($club->getClubHistories()[0]->getClubHistoryStatus() == 2)
                {
                    $club->getClubHistories()[0]->setClubHistoryStatus(1);
                    $club->getClubHistories()[0]->setClubHistoryUpdate($form->get('MembershipDate')->getData());
                }
                else if ($club->getClubHistories()[0]->getClubHistoryStatus() == 3)
                {
                    $club->getClubHistories()[1]->setClubHistoryStatus(1);
                    $club->getClubHistories()[1]->setClubHistoryUpdate($form->get('MembershipDate')->getData());
                }
            }
            else
            {
                if ($club->getClubHistories()[0]->getClubHistoryStatus() == 1)
                {
                    $club->getClubHistories()[0]->setClubHistoryStatus(2);
                }
                else if ($club->getClubHistories()[0]->getClubHistoryStatus() == 3)
                {
                    $club->getClubHistories()[1]->setClubHistoryStatus(2);

                    $entityManager->remove($club->getClubHistories()[0]);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/historyEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/ajouter-un-gestionnaire', name:'managersAdd')]
    public function managersAdd(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-ManagerAdd'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $manager = new ClubManager();

        $form = $this->createForm(ClubType::class, $manager, array('formData' => array('Form' => 'Manager', 'Action' => 'Add'), 'data_class' => ClubManager::class, 'action' => $this->generateUrl('club-managersAdd', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            if ($form->get('ClubManagerIsMain')->getData())
            {
                foreach($club->getClubManagers() as $clubManager)
                {
                    $clubManager->setClubManagerIsMain(null);
                }
            }

            if (!is_null($form->get('ClubManagerMember')->getData()))
            {
                $member = $doctrine->getRepository(Member::class)->findOneBy(['member_id' => $form->get('ClubManagerMember')->getData()]);

                if (!is_null($member))
                {
                    $manager->setClubManagerClub($club);
                    $manager->setClubManagerMember($member);

                    $entityManager->persist($manager);
                }
            }

            if (!is_null($form->get('ClubManagerLogin')->getData()))
            {
                $user = $doctrine->getRepository(User::class)->findOneBy(['login' => $form->get('ClubManagerLogin')->getData()]);

                if ((!is_null($user)) && (is_null($user->getUserMember())))
                {
                    $manager->setClubManagerClub($club);
                    $manager->setClubManagerUser($user);

                    $entityManager->persist($manager);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/managerAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ClubManager $manager
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/gestionnaire/{manager<\d+>}/modifier', name:'managersEdit')]
    public function managersEdit(Access $access, Club $club, ClubManager $manager, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-ManagerEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        if ($manager->getClubManagerClub() !== $club)
        {
            die();
        }

        $formDelete = $this->createForm(ClubType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('club-managersEdit', array('club' => $club->getClubId(), 'manager' => $manager->getClubManagerId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($manager);
            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        foreach($club->getClubManagers() as $clubManager)
        {
            $clubManager->setClubManagerIsMain(null);
        }

        $formEdit = $this->createForm(ClubType::class, $manager, array('formData' => array('Form' => 'Manager', 'Action' => 'Edit'), 'data_class' => ClubManager::class, 'action' => $this->generateUrl('club-managersEdit', array('club' => $club->getClubId(), 'manager' => $manager->getClubManagerId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/managerEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/modifier-details-association', name:'associationEdit')]
    public function associationEdit(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-AssociationEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(ClubType::class, $club, array('formData' => array('Form' => 'Association'), 'data_class' => Club::class, 'action' => $this->generateUrl('club-associationEdit', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/associationEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/modifier-details-reseaux-sociaux', name:'socialsEdit')]
    public function socialsEdit(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-SocialEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(ClubType::class, $club, array('formData' => array('Form' => 'Socials'), 'data_class' => Club::class, 'action' => $this->generateUrl('club-socialsEdit', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/socialEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/modifier-details-site-internet', name:'websiteEdit')]
    public function websiteEdit(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-WebsiteEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(ClubType::class, $club, array('formData' => array('Form' => 'Website'), 'data_class' => Club::class, 'action' => $this->generateUrl('club-websiteEdit', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/websiteEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/modifier-details-du-comite', name:'committeeEdit')]
    public function commiteeEdit(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-CommiteeEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(ClubType::class, $club, array('formData' => array('Form' => 'Commitee'), 'data_class' => Club::class, 'action' => $this->generateUrl('club-committeeEdit', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/committeeEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/modifier-le-logo-du-club', name:'photoEdit')]
    public function photoEdit(Access $access, Club $club, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-PhotoEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(ClubType::class, null, array('formData' => array('Form' => 'Photo'), 'action' => $this->generateUrl('club-photoEdit', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $photo = null;

            $file = $form['ClubPhoto']->getData();

            $fileName = $club->getClubId().'.'.$file->guessExtension();

            $file->move('uploads/clubs/original', $fileName);

            $image_info = getimagesize('uploads/clubs/original/'.$fileName);

            if( $image_info[2] == IMAGETYPE_JPEG )
            {
                $photo = imagecreatefromjpeg('uploads/clubs/original/'.$fileName);
            }
            elseif( $image_info[2] == IMAGETYPE_GIF )
            {
                $photo = imagecreatefromgif('uploads/clubs/original/'.$fileName);
            }
            elseif( $image_info[2] == IMAGETYPE_PNG )
            {
                $photo = imagecreatefrompng('uploads/clubs/original/'.$fileName);
            }
            elseif( $image_info[2] == IMAGETYPE_WEBP )
            {
                $photo = imagecreatefromwebp('uploads/clubs/original/'.$fileName);
            }

            $ratio = $image_info[0] / $image_info[1];

            if ($image_info[0] > 780 || $image_info[1] > 460 )
            {
                if (780 / 460 > $ratio)
                {
                    $height = 460;
                    $width = 460 * $ratio;
                }
                else
                {
                    $height = 780 / $ratio;
                    $width = 780;
                }
            }
            else
            {
                $width  = $image_info[0];
                $height = $image_info[1];
            }

            $new_photo = imagecreatetruecolor($width, $height);

            imagecopyresampled($new_photo, $photo, 0, 0, 0, 0, $width, $height, $image_info[0], $image_info[1]);

            imagepng($new_photo, 'uploads/clubs/'.$club->getClubId().'.png');

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/photoEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/modifier-la-cotisation/{member<\d+>}', name:'subscriptionEdit')]
    public function subscriptionEdit(Access $access, Club $club, ManagerRegistry $doctrine, Member $member, Request $request, Session $session): Response
    {
        if (!$access->check('Club-SubscriptionEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(ClubType::class, $member, array('formData' => array('Form' => 'Subscription'), 'data_class' => Member::class, 'action' => $this->generateUrl('club-subscriptionEdit', array('club' => $club->getClubId(), 'member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($member->getMemberSubscriptionStatus() != 2)
            {
                $member->setMemberSubscriptionValidity(null);
            }

            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        return $this->render('Club/Modal/subscriptionEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @param int|null $number
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/ajouter-un-cours/{number<\d+>}', name:'lessonAdd')]
    public function lessonAdd(Access $access, Club $club, ManagerRegistry $doctrine, Request $request, Session $session, ?int $number = null): RedirectResponse|Response
    {
        if (!$access->check('Club-LessonAdd'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $class = $club->getClubClasses();

        $lesson = new Lesson();

        $lesson->setLessonClub($club);

        if (is_null($number))
        {
            $form = $this->createForm(ClubType::class, $lesson, array('formData' => array('Form' => 'Lesson', 'Action' => 'Add'), 'data_class' => Lesson::class, 'action' => $this->generateUrl('club-lessonAdd', array('club' => $club->getClubId()))));

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $duration = date_diff($form->get('LessonEndingHour')->getData(), $lesson->getLessonStartingHour());

                $lesson->setLessonDuration($duration->h * 60 + $duration->i);

                if (is_null($doctrine->getRepository(Lesson::class)->findOneBy(array('lesson_club' => $club->getClubId(), 'lesson_date' => $lesson->getLessonDate(), 'lesson_starting_hour' => $lesson->getLessonStartingHour(), 'lesson_type' => $lesson->getLessonType()))))
                {
                    $entityManager = $doctrine->getManager();

                    $entityManager->persist($lesson);
                    $entityManager->flush();
                }
                else
                {
                    $this->addFlash('warning', 'Il y a déjà un cours qui été créé à cette date');
                }

                return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
            }

            return $this->render('Club/Modal/lessonAdd.html.twig', array('form' => $form->createView()));
        }
        elseif (isset($class[$number-1]))
        {
            $today = new DateTime();

            $duration = date_diff($class[$number-1]->getClubClassEndingHour(), $class[$number-1]->getClubClassStartingHour());

            $lesson->setLessonStartingHour($class[$number-1]->getClubClassStartingHour());
            $lesson->setLessonDuration($duration->h * 60 + $duration->i);
            $lesson->setLessonType($class[$number-1]->getClubClassType());

            $day = $class[$number-1]->getClubClassDay();

            if (intval($today->format('N')) == $day)
            {
                $lesson->setLessonDate(new DateTime());
            }
            elseif($day == 1)
            {
                $lesson->setLessonDate(new DateTime('next monday'));
            }
            elseif($day == 2)
            {
                $lesson->setLessonDate(new DateTime('next tuesday'));
            }
            elseif($day == 3)
            {
                $lesson->setLessonDate(new DateTime('next wednesday'));
            }
            elseif($day == 4)
            {
                $lesson->setLessonDate(new DateTime('next thursday'));
            }
            elseif($day == 5)
            {
                $lesson->setLessonDate(new DateTime('next friday'));
            }
            elseif($day == 6)
            {
                $lesson->setLessonDate(new DateTime('next saturday'));
            }
            elseif($day == 7)
            {
                $lesson->setLessonDate(new DateTime('next sunday'));
            }

            if (is_null($doctrine->getRepository(Lesson::class)->findOneBy(array('lesson_club' => $club->getClubId(), 'lesson_date' => $lesson->getLessonDate(), 'lesson_starting_hour' => $lesson->getLessonStartingHour(), 'lesson_type' => $lesson->getLessonType()))))
            {
                $entityManager = $doctrine->getManager();

                $entityManager->persist($lesson);
                $entityManager->flush();
            }
            else
            {
                $this->addFlash('warning', 'Le prochain cours à ce moment existe déjà');
            }
        }

        return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Lesson $lesson
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param Session $session
     * @return RedirectResponse|Response
     */
    #[Route('/{club<\d+>}/modifier-un-cours/{lesson<\d+>}', name:'lessonEdit')]
    public function lessonEdit(Access $access, Club $club, Lesson $lesson, ManagerRegistry $doctrine, Request $request, Session $session): RedirectResponse|Response
    {
        if (!$access->check('Club-LessonEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        if ($lesson->getLessonClub() !== $club)
        {
            die();
        }

        $formDelete = $this->createForm(ClubType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('club-lessonEdit', array('club' => $club->getClubId(), 'lesson' => $lesson->getLessonId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($lesson);
            $entityManager->flush();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId()));
        }

        $formEdit = $this->createForm(ClubType::class, $lesson, array('formData' => array('Form' => 'Lesson', 'Action' => 'Edit'), 'data_class' => Lesson::class, 'action' => $this->generateUrl('club-lessonEdit', array('club' => $club->getClubId(), 'lesson' => $lesson->getLessonId()))));

        $endHour = date_add(clone $lesson->getLessonStartingHour(), DateInterval::createFromDateString($lesson->getLessonDuration() . ' minute'));

        $formEdit->get('LessonEndingHour')->setData($endHour);

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            if ((($lesson->getLessonDate() == $formEdit->get('LessonDate')->getData()) && ($lesson->getLessonStartingHour() == $formEdit->get('LessonStartingHour')->getData())) || (is_null($doctrine->getRepository(Lesson::class)->findOneBy(array('lesson_club' => $club->getClubId(), 'lesson_date' => $lesson->getLessonDate(), 'lesson_starting_hour' => $lesson->getLessonStartingHour())))))
            {
                $duration = date_diff($formEdit->get('LessonEndingHour')->getData(), $formEdit->get('LessonStartingHour')->getData());

                $lesson->setLessonDuration($duration->h * 60 + $duration->i);

                $entityManager = $doctrine->getManager();

                $entityManager->flush();
            }
            else
            {
                $this->addFlash('warning', 'Le prochain cours à ce moment existe déjà');
            }

            return $this->redirectToRoute('club-lessonIndex', array('club' => $club->getClubId(), 'lesson' => $lesson->getLessonId()));
        }

        return $this->render('Club/Modal/lessonEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Lesson $lesson
     * @param ManagerRegistry $doctrine
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/details-des-presences/{lesson<\d+>}', name:'lessonIndex')]
    public function lessonIndex(Access $access, Club $club, Lesson $lesson, ManagerRegistry $doctrine, Session $session): Response
    {
        if (!$access->check('Club-LessonIndex'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $data['Attendance'] = array();

        foreach ($lesson->getLessonAttendances() as $attendance)
        {
            $data['Attendance'][$attendance->getLessonAttendanceId()] = $attendance->getLessonAttendanceMember();
        }

        $data['ActiveMemberList'] = array();

        foreach ($doctrine->getRepository(Member::class)->getClubActiveMemberList($club, null, $lesson->getLessonDate()) as $member)
        {
            if (in_array($member, $data['Attendance']))
            {
                continue;
            }
            elseif ($member->getMemberSubscriptionStatus() == 3)
            {
                continue;
            }
            elseif (($lesson->getLessonType() == 1 && $member->getMemberSubscriptionList() == 2) || ($lesson->getLessonType() == 2 && $member->getMemberSubscriptionList() == 1))
            {
                continue;
            }

            $data['ActiveMemberList'][] = $member;
        }

        $data['Club']   = $club;
        $data['Lesson'] = $lesson;

        return $this->render('Club/lessonIndex.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Lesson $lesson
     * @param ManagerRegistry $doctrine
     * @param Session $session
     * @param Member|null $member
     * @return Response
     */
    #[Route('/{club<\d+>}/details-des-presences/{lesson<\d+>}/ajouter/{member<\d+>}', name:'attendanceAdd')]
    public function attendanceAdd(Access $access, Club $club, Lesson $lesson, ManagerRegistry $doctrine, Session $session, ?Member $member = null): Response
    {
        if (!$access->check('Club-AttendanceAdd'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $entityManager = $doctrine->getManager();

        $attendance = new LessonAttendance();

        $attendance->setLesson($lesson);

        if (is_null($member))
        {
            $attendance->setLessonAttendanceName('Invité');
        }
        else
        {
            $attendance->setLessonAttendanceMember($member);
        }

        $entityManager->persist($attendance);
        $entityManager->flush();

        return $this->redirectToRoute('club-lessonIndex', array('club' => $club->getClubId(), 'lesson' => $lesson->getLessonId()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Lesson $lesson
     * @param LessonAttendance $attendance
     * @param ManagerRegistry $doctrine
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/details-des-presences/{lesson<\d+>}/retirer/{attendance<\d+>}', name:'attendanceDelete')]
    public function attendanceDelete(Access $access, Club $club, Lesson $lesson, LessonAttendance $attendance, ManagerRegistry $doctrine, Session $session): Response
    {
        if (!$access->check('Club-AttendanceDelete'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $entityManager = $doctrine->getManager();

        $entityManager->remove($attendance);
        $entityManager->flush();

        return $this->redirectToRoute('club-lessonIndex', array('club' => $club->getClubId(), 'lesson' => $lesson->getLessonId()));
    }

    /**
     * @param Access $access
     * @param Club $club
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{club<\d+>}/liste-anciens-membres', name:'secretariatOld')]
    public function secretariatOld(Access $access, Club $club, Request $request, Session $session): Response
    {
        if (!$access->check('Club-SecretariatOld'))
        {
            die();
        }

        if ($session->has('Club') && ($club->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(ClubType::class, null, array('formData' => array('Form' => 'SecretariatOld'), 'action' => $this->generateUrl('club-secretariatOld', array('club' => $club->getClubId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $date = $form->get('Date')->getData();

            return $this->redirectToRoute('club-index', array('club' => $club->getClubId(), 'date' => $date->format('Ymd')));
        }

        $data['Club'] = $club;

        return $this->render('Club/Modal/secretariatOld.html.twig', array('form' => $form->createView(), 'data' => $data));
    }
}
