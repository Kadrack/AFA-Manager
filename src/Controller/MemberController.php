<?php
// src/Controller/MemberController.php
namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Grade;
use App\Entity\ClubLesson;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\Title;

use App\Form\MemberType;

use App\Service\Access;
use App\Service\EmailSender;
use App\Service\FileGenerator;
use App\Service\ListData;
use App\Service\PhotoUploader;
use App\Service\SearchMember;

use DateTime;

use Doctrine\Persistence\ManagerRegistry;

use Exception;

use Picqer\Barcode\BarcodeGeneratorPNG;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\ExpressionLanguage\Expression;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class MemberController
 * @package App\Controller
 */
#[Route('/membre', name:'member-')]
#[IsGranted(new Expression('is_granted("ROLE_USER")'))]
class MemberController extends AbstractController
{
    /**
     * @param Access $access
     * @param Request $request
     * @param SearchMember $search
     * @param Session $session
     * @return Response
     */
    #[Route('/rechercher-membres', name:'search')]
    public function search(Access $access, Request $request, SearchMember $search, Session $session): Response
    {
        if (!$session->has('Id') && !is_null($this->getUser()->getMember()))
        {
            return $this->redirectToRoute('member-index', array('member' => $this->getUser()->getMember()->getMemberId()));
        }

        if (!$access->check('Search-Member'))
        {
            die();
        }

        $data = array();

        $form = $this->createForm(MemberType::class, null, array('formData' => array('Form' => 'Search')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $data['Results'] = $search->getResults($form->get('Search')->getData());

            return $this->render('Member/search.html.twig', array('form' => $form->createView(), 'data' => $data));
        }

        return $this->render('Member/search.html.twig', array('form' => $form->createView(), 'data' => $data));
    }

    /**
     * @param Access $access
     * @param Member $member
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}', name:'index')]
    public function index(Access $access, Member $member, Session $session): Response
    {
        if (!$access->check('Member-Index'))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberOutdate() || ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId())))
        {
            die();
        }

        $data['Member'] = $member;

        return $this->render('Member/index.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Member $member
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/onglet-licence', name:'licenceTab')]
    public function licenceTab(Access $access, Member $member, Session $session): Response
    {
        if (!$access->check('Member-LicenceTab'))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeMemberTab', 'licenceTab');

        $data['Member'] = $member;

        return $this->render('Member/Tab/licence.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Member $member
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/onglet-grade', name:'gradeTab')]
    public function gradeTab(Access $access, Member $member, Session $session): Response
    {
        if (!$access->check('Member-TrainingTab'))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeMemberTab', 'gradeTab');

        $data['Member'] = $member;

        return $this->render('Member/Tab/grade.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/onglet-stage', name:'trainingTab')]
    public function trainingTab(Access $access, ManagerRegistry $doctrine, Member $member, Session $session): Response
    {
        if (!$access->check('Member-TrainingTab'))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeMemberTab', 'trainingTab');

        $data['Member']            = $member;
        $data['Stages']['Total']   = 0;
        $data['Stages']['History'] = array();
        $data['Club']              = $member->getMemberActualClub();

        $stage_history = $doctrine->getRepository(Member::class)->getMemberAttendances($data['Member']->getMemberId());

        $stage_count = 0;

        $grades = $data['Member']->getMemberGrades();

        for ($i = 0; $i < sizeof($grades); $i++)
        {
            $data['Stages']['History'][$i]['Total'] = 0;
            $data['Stages']['History'][$i]['Rank']  = $grades[$i]->getGradeRank();

            while (isset($stage_history[$stage_count]))
            {
                $date = is_null($grades[$i]->getGradeDate()) ? new DateTime() : $grades[$i]->getGradeDate();

                if (($date > $stage_history[$stage_count]['Date']) && ($i != sizeof($grades) - 1))
                {
                    break;
                }

                $stage_history[$stage_count]['Duration'] = $stage_history[$stage_count]['Duration'] / 60;

                $data['Stages']['History'][$i]['Total']   = $data['Stages']['History'][$i]['Total'] + $stage_history[$stage_count]['Duration'];
                $data['Stages']['History'][$i]['Stage'][] = $stage_history[$stage_count];

                $data['Stages']['Total'] = $data['Stages']['Total'] + $stage_history[$stage_count]['Duration'];

                $stage_count++;
            }
        }

        $data['LessonCount'] = $doctrine->getRepository(ClubLesson::class)->getLessonCount($data['Member']);

        return $this->render('Member/Tab/training.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Member $member
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/onglet-titre', name:'titleTab')]
    public function titleTab(Access $access, Member $member, Session $session): Response
    {
        if (!$access->check('Member-TitleTab'))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeMemberTab', 'titleTab');

        $data['Member'] = $member;

        return $this->render('Member/Tab/title.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param EmailSender $emailSender
     * @param Member $member
     * @param Request $request
     * @param Session $session
     * @return Response
     * @throws TransportExceptionInterface
     */
    #[Route('/{member<\d+>}/onglet-email', name:'emailTab')]
    public function emailTab(Access $access, EmailSender $emailSender, Member $member, Request $request, Session $session): Response
    {
        if (!$access->check('Member-EmailTab'))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeMemberTab', 'emailTab');

        $form = $this->createForm(MemberType::class, null, array('formData' => array('Form' => 'Mailing'), 'action' => $this->generateUrl('member-emailTab', array('member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $emailSender->setSubject($form['Subject']->getData());
            $emailSender->setContent($form['Text']->getData());

            is_null($form['Attachment']->getData()) ?: $emailSender->setAttachment($form['Attachment']->getData());

            $emailSender->toMember($member, $form['Manager']->getData() == 1, $form['DojoCho']->getData() == 1);

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Tab/email.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Member $member
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/onglet-personnelle', name:'personalTab')]
    public function personalTab(Access $access, Member $member, Session $session): Response
    {
        if (!$access->check('Member-PersonalTab') && $this->getUser()->getMember()->getMemberId() != $member->getMemberId())
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $session->set('activeMemberTab', 'personalTab');

        $data['Member'] = $member;

        return $this->render('Member/Tab/personal.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @param Session $session
     * @return Response
     * @throws Exception
     */
    #[Route('/{member<\d+>}/ajouter-un-renouvellement', name:'licencesAdd')]
    public function licenceAdd(Access $access, ManagerRegistry $doctrine, Member $member, Request $request, Session $session): Response
    {
        if (!($access->check('Member-LicenceAdd')))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $new = false;

        if (is_null($member->getMemberLicences()[0]->getMemberLicenceDeadline()) && ($member->getMemberLicences()[0]->getMemberLicenceUpdate() > DateTime::createFromFormat("Ymd", '20211001')))
        {
            $licence = $member->getMemberLicences()[0];
        }
        else
        {
            $new = true;

            $licence = new MemberLicence();

            $licence->setMemberLicenceMember($member);
            $licence->setMemberLicenceClub($member->getMemberLicences()[0]->getMemberLicenceClub());
        }

        $licence->setMemberLicenceDeadline(new DateTime('+1 year '.$member->getMemberLicences()[0]->getMemberLicenceDeadline()->format('Y-m-d')));
        $licence->setMemberLicenceUpdate(new DateTime());

        $form = $this->createForm(MemberType::class, $licence, array('formData' => array('Form' => 'Licence', 'Action' => 'Add'), 'data_class' => MemberLicence::class, 'action' => $this->generateUrl('member-licencesAdd', array('member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            !$new ?: $entityManager->persist($licence);

            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/licencesAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param MemberLicence $licence
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/licences/{licence<\d+>}/modifier', name:'licencesEdit')]
    public function licencesEdit(Access $access, ManagerRegistry $doctrine, Member $member, MemberLicence $licence, Request $request, Session $session): Response
    {
        if (!$access->check('Member-LicenceEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $licence = $doctrine->getRepository(MemberLicence::class)->findOneBy(['memberLicenceId' => $licence->getMemberLicenceId()]);

        $formDelete = $this->createForm(MemberType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('member-licencesEdit', array('member' => $member->getMemberId(), 'licence' => $licence->getMemberLicenceId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($licence);
            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        $formEdit = $this->createForm(MemberType::class, $licence, array('formData' => array('Form' => 'Licence', 'Action' => 'Edit'), 'data_class' => MemberLicence::class, 'action' => $this->generateUrl('member-licencesEdit', array('member' => $member->getMemberId(), 'licence' => $licence->getMemberLicenceId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/licencesEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param FileGenerator $fileGenerator
     * @param Member $member
     * @param Session $session
     * @return BinaryFileResponse|Response
     */
    #[Route('/{member<\d+>}/formulaire-renouvellement/', name:'licencesFormPrint')]
    public function licencesFormPrint(Access $access, FileGenerator $fileGenerator, Member $member, Session $session): BinaryFileResponse|Response
    {
        if (!($access->check('Member-LicenceFormPrint')) && $this->getUser()->getMember()->getMemberId() != $member->getMemberId())
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $data['Member'] = $member;

        $licenceForm = $this->renderView('Member/Print/licenceForm.html.twig', array('data' => $data));

        $filename = str_replace(' ', '', $member->getMemberId().'-'.$member->getMemberName().'.pdf');

        $pdf = $fileGenerator->pdfGenerator($this->getParameter('kernel.project_dir').'/private/' . $filename, $licenceForm);

        $response = new BinaryFileResponse($pdf);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response->deleteFileAfterSend();
    }

    /**
     * @param Access $access
     * @param Member $member
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/imprimer-timbres', name:'licencesStampPrint')]
    public function licencesStampPrint(Access $access, Member $member, Session $session): Response
    {
        if (!($access->check('Member-LicenceStampPrint')) && $this->getUser()->getMember()->getMemberId() != $member->getMemberId())
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        if (is_null($member->getMemberLicences()[0]->getMemberLicencePrintoutCreation()))
        {
            $data['Licence'] = $member->getMemberLicences()[1];
        }
        else
        {
            $data['Licence'] = $member->getMemberLicences()[0];
        }

        $data['Grade'] = $member->getMemberGrades()[0]->getGradeRank();

        return $this->render('Member/Print/stamps.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Member $member
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/imprimer-cartes', name:'licencesCardPrint')]
    public function licencesCardPrint(Access $access, Member $member, Session $session): Response
    {
        if (!$access->check('Member-LicenceCardPrint'))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $generator = new BarcodeGeneratorPNG();

        $barcode = base64_encode($generator->getBarcode($member->getMemberId(), $generator::TYPE_CODE_93));

        $data['Member']  = $member;
        $data['Barcode'] = $barcode;

        return $this->render('Member/Print/cards.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param ListData $listData
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/ajouter-un-grade', name:'gradesAdd')]
    public function gradesAdd(Access $access, ManagerRegistry $doctrine, Member $member, ListData $listData, Request $request, Session $session): Response
    {
        if (!($access->check('Member-GradeAdd')))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $grade = new Grade();

        if ($access->check('Member-GradeKyuEdit') && $access->check('Member-GradeDanEdit'))
        {
            $choices = $listData->getGrade();
        }
        elseif ($access->check('Member-GradeKyuEdit'))
        {
            $choices = $listData->getGradeKyu();
        }
        elseif ($access->check('Member-GradeDanEdit'))
        {
            $choices = $listData->getGradeDan();
        }

        $form = $this->createForm(MemberType::class, $grade, array('formData' => array('Form' => 'Grade', 'Action' => 'Add', 'IsFromExamSession' => false, 'Choices' => $choices), 'data_class' => Grade::class, 'action' => $this->generateUrl('member-gradesAdd', array('member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $grade->setGradeMember($member);
            $grade->setGradeClub($member->getMemberActualClub());

            $entityManager = $doctrine->getManager();

            $entityManager->persist($grade);
            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/gradesAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Grade $grade
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param ListData $listData
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/grades/{grade<\d+>}/modifier', name:'gradesEdit')]
    public function gradesEdit(Access $access, Grade $grade, ManagerRegistry $doctrine, Member $member, ListData $listData, Request $request, Session $session): Response
    {
        if (!$access->check('Member-GradeKyuEdit') && !$access->check('Member-GradeDanEdit'))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $grade = $doctrine->getRepository(Grade::class)->findOneBy(['gradeId' => $grade->getGradeId()]);

        $choices = array();

        if ($access->check('Member-GradeKyuEdit') && $access->check('Member-GradeDanEdit'))
        {
            $choices = $listData->getGrade();
        }
        elseif ($access->check('Member-GradeKyuEdit'))
        {
            $choices = $listData->getGradeKyu();
        }
        elseif ($access->check('Member-GradeDanEdit'))
        {
            $choices = $listData->getGradeDan();
        }

        if (!is_null($grade->getGradeSession()))
        {
            $formDelete = null;
        }
        else
        {
            $formDelete = $this->createForm(MemberType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('member-gradesEdit', array('member' => $member->getMemberId(), 'grade' => $grade->getGradeId()))));

            $formDelete->handleRequest($request);

            if ($formDelete->isSubmitted() && $formDelete->isValid())
            {
                $entityManager = $doctrine->getManager();

                $entityManager->remove($grade);
                $entityManager->flush();

                return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
            }
        }

        $formEdit = $this->createForm(MemberType::class, $grade, array('formData' => array('Form' => 'Grade', 'Action' => 'Edit', 'IsFromExamSession' => !is_null($grade->getGradeSession()), 'Choices' => $choices), 'data_class' => Grade::class, 'action' => $this->generateUrl('member-gradesEdit', array('member' => $member->getMemberId(), 'grade' => $grade->getGradeId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/gradesEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete?->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/modifier-debut-de-pratique', name:'gradesStartEdit')]
    public function gradesStartEdit(Access $access, ManagerRegistry $doctrine, Member $member, Request $request, Session $session): Response
    {
        if (!($access->check('Member-GradeStartEdit')))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(MemberType::class, $member, array('formData' => array('Form' => 'StartPractice'), 'data_class' => Member::class,'action' => $this->generateUrl('member-gradesStartEdit', array('member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/gradesStartEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/ajouter-un-titre', name:'titlesAdd')]
    public function titlesAdd(Access $access, ManagerRegistry $doctrine, Member $member, Request $request, Session $session): Response
    {
        if (!($access->check('Member-TitleAdd')))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $title = new Title();

        $form = $this->createForm(MemberType::class, $title, array('formData' => array('Form' => 'Title', 'Action' => 'Add'), 'data_class' => Title::class, 'action' => $this->generateUrl('member-titlesAdd', array('member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $title->setTitleMember($member);

            $entityManager = $doctrine->getManager();

            $entityManager->persist($title);
            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/titlesAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @param Session $session
     * @param Title $title
     * @return Response
     */
    #[Route('/{member<\d+>}/titres/{title<\d+>}/modifier', name:'titlesEdit')]
    public function titlesEdit(Access $access, ManagerRegistry $doctrine, Member $member, Request $request, Session $session, Title $title): Response
    {
        if (!($access->check('Member-TitleEdit')))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $title = $doctrine->getRepository(Title::class)->findOneBy(['titleId' => $title->getTitleId()]);

        $formDelete = $this->createForm(MemberType::class, $title, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('member-titlesEdit', array('member' => $member->getMemberId(), 'title' => $title->getTitleId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($title);
            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        $formEdit = $this->createForm(MemberType::class, $title, array('formData' => array('Form' => 'Title', 'Action' => 'Edit'), 'data_class' => Title::class, 'action' => $this->generateUrl('member-titlesEdit', array('member' => $member->getMemberId(), 'title' => $title->getTitleId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/titlesEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/ajouter-une-formation', name:'formationsAdd')]
    public function formationsAdd(Access $access, ManagerRegistry $doctrine, Member $member, Request $request, Session $session): Response
    {
        if (!($access->check('Member-FormationAdd')))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $formation = new Formation();

        $form = $this->createForm(MemberType::class, $formation, array('formData' => array('Form' => 'Formation', 'Action' => 'Add'), 'data_class' => Formation::class, 'action' => $this->generateUrl('member-formationsAdd', array('member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $formation->setFormationMember($member);

            $entityManager = $doctrine->getManager();

            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/formationsAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Formation $formation
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/formations/{formation<\d+>}/modifier', name:'formationsEdit')]
    public function formationsEdit(Access $access, Formation $formation, ManagerRegistry $doctrine, Member $member, Request $request, Session $session): Response
    {
        if (!($access->check('Member-FormationEdit')))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $formation = $doctrine->getRepository(Formation::class)->findOneBy(['formationId' => $formation->getFormationId()]);

        $formDelete = $this->createForm(MemberType::class, $formation, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('member-formationsEdit', array('member' => $member->getMemberId(), 'formation' => $formation->getFormationId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($formation);
            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        $formEdit = $this->createForm(MemberType::class, $formation, array('formData' => array('Form' => 'Formation', 'Action' => 'Edit'), 'data_class' => Formation::class, 'action' => $this->generateUrl('member-formationsEdit', array('member' => $member->getMemberId(), 'formation' => $formation->getFormationId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/formationsEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param PhotoUploader $photoUploader
     * @param Request $request
     * @param Session $session
     * @param int $data
     * @return Response
     */
    #[Route('/{member<\d+>}/modifier-donnees-personnelles/{data<\d+>}', name:'personalsDataEdit')]
    public function personalsDataEdit(Access $access, ManagerRegistry $doctrine, Member $member, PhotoUploader $photoUploader, Request $request, Session $session, int $data): Response
    {
        if (!$access->check('Member-PersonalTab') && $this->getUser()->getMember()->getMemberId() != $member->getMemberId())
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        switch ($data)
        {
            case 1 :
                $form = $this->createForm(MemberType::class, $member, array('formData' => array('Form' => 'Photo'), 'data_class' => Member::class, 'action' => $this->generateUrl('member-personalsDataEdit', array('member' => $member->getMemberId(), 'data' => 1))));
                break;
            case 2 :
                $form = $this->createForm(MemberType::class, $member, array('formData' => array('Form' => 'Name'), 'data_class' => Member::class, 'action' => $this->generateUrl('member-personalsDataEdit', array('member' => $member->getMemberId(), 'data' => 2))));
                break;
            case 3 :
                $form = $this->createForm(MemberType::class, $member, array('formData' => array('Form' => 'Birthday'), 'data_class' => Member::class, 'action' => $this->generateUrl('member-personalsDataEdit', array('member' => $member->getMemberId(), 'data' => 3))));
                break;
            case 4 :
                $form = $this->createForm(MemberType::class, $member, array('formData' => array('Form' => 'Sex'), 'data_class' => Member::class, 'action' => $this->generateUrl('member-personalsDataEdit', array('member' => $member->getMemberId(), 'data' => 4))));
                break;
            case 5 :
                $form = $this->createForm(MemberType::class, $member, array('formData' => array('Form' => 'Address'), 'data_class' => Member::class, 'action' => $this->generateUrl('member-personalsDataEdit', array('member' => $member->getMemberId(), 'data' => 5))));
                break;
            case 6 :
                $form = $this->createForm(MemberType::class, $member, array('formData' => array('Form' => 'Phone'), 'data_class' => Member::class, 'action' => $this->generateUrl('member-personalsDataEdit', array('member' => $member->getMemberId(), 'data' => 6))));
                break;
            case 7 :
                $form = $this->createForm(MemberType::class, $member, array('formData' => array('Form' => 'Email'), 'data_class' => Member::class, 'action' => $this->generateUrl('member-personalsDataEdit', array('member' => $member->getMemberId(), 'data' => 7))));
                break;
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($data == 1)
            {
                $member->setMemberPhoto(is_null($form['MemberPhoto']->getData()) ?: $photoUploader->upload($form['MemberPhoto']->getData()));
            }

            if ($data == 6)
            {
                if (strlen($form['MemberPhone']->getData()) < 6)
                {
                    $member->setMemberPhone();
                }
            }

            if ($data == 7)
            {
                if (strlen($form['MemberEmail']->getData()) < 6)
                {
                    $member->setMemberEmail();
                }
            }

            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/personalsDataEdit.html.twig', array('form' => $form->createView(), 'data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Member $member
     * @param Request $request
     * @param Session $session
     * @return Response
     */
    #[Route('/{member<\d+>}/modifier-id-aikikai/', name:'aikikaiIdEdit')]
    public function aikikaiIdEdit(Access $access, ManagerRegistry $doctrine, Member $member, Request $request, Session $session): Response
    {
        if (!($access->check('Member-AikikaiIdEdit')))
        {
            die();
        }

        if ($session->has('Club') && ($member->getMemberActualClub()->getClubId() != $session->get('Club')->getClubId()))
        {
            die();
        }

        $form = $this->createForm(MemberType::class, $member, array('formData' => array('Form' => 'AikikaiId'), 'data_class' => Member::class, 'action' => $this->generateUrl('member-aikikaiIdEdit', array('member' => $member->getMemberId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('member-index', array('member' => $member->getMemberId()));
        }

        return $this->render('Member/Modal/aikikaiIdEdit.html.twig', array('form' => $form->createView()));
    }
}
