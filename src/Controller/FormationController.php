<?php
// src/Controller/FormationController.php
namespace App\Controller;

use App\Entity\FormationSession;
use App\Entity\FormationSessionCandidate;
use App\Entity\Member;

use App\Form\FormationType;

use App\Service\Access;

use DateTime;

use Doctrine\ORM\EntityManagerInterface;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommonController
 * @package App\Controller
 */
#[Route('/formation', name:'formation-')]
class FormationController extends AbstractController
{
    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/', name:'list')]
    public function list(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('Formation-SessionList'))
        {
            die();
        }

        $data['Sessions'] = $doctrine->getRepository(FormationSession::class)->findBy(array(), array('formationSessionDate' => 'DESC', 'formationSessionType' => 'ASC'));

        $data['Waiting'] = $doctrine->getRepository(FormationSession::class)->findBy(array('formationSessionDate' => null), array('formationSessionDate' => 'DESC', 'formationSessionType' => 'ASC'));

        return $this->render('Formation/list.html.twig', array('data' => $data));
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
        if (!$access->check('Formation-SessionAdd'))
        {
            die();
        }

        $formationSession = new FormationSession();

        $form = $this->createForm(FormationType::class, $formationSession, array('formData' => array('Form' => 'Session', 'Action' => 'Add'), 'data_class' => FormationSession::class, 'action' => $this->generateUrl('formation-sessionAdd')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->persist($formationSession);
            $entityManager->flush();

            return $this->redirectToRoute('formation-list');
        }

        return $this->render('/Formation/Modal/sessionsAdd.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param FormationSession $formationSession
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/modifier-une-session/{formationSession<\d+>}', name:'sessionEdit')]
    public function sessionEdit(Access $access, FormationSession $formationSession, ManagerRegistry $doctrine, Request $request): Response
    {
        if (!$access->check('Formation-SessionEdit'))
        {
            die();
        }

        $form = $this->createForm(FormationType::class, $formationSession, array('formData' => array('Form' => 'Session', 'Action' => 'Edit'), 'data_class' => FormationSession::class, 'action' => $this->generateUrl('formation-sessionEdit', array('formationSession' => $formationSession->getFormationSessionId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('formation-list', array('formationSession' => $formationSession->getFormationSessionId()));
        }

        return $this->render('/Formation/Modal/sessionsEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param int $type
     * @param int|null $formType
     * @return Response
     */
    #[Route('/inscription-a-la-session/{type<\d+>}/{formType<\d+>}', name:'subscription')]
    public function subscription(ManagerRegistry $doctrine, Request $request, int $type, ?int $formType = 0): Response
    {
        if ($type < 4 || $type > 6)
        {
            die();
        }

        $data['FormType'] = $formType;

        $entityManager = $doctrine->getManager();

        $sessions = $doctrine->getRepository(FormationSession::class)->findBy(array('formationSessionType' => $type), array('formationSessionDate' => 'DESC'));

        if ($sessions[array_key_first($sessions)]->getFormationSessionOpen() >= new DateTime() || $sessions[array_key_first($sessions)]->getFormationSessionIsOpen())
        {
            $data['Session'] = $sessions[array_key_first($sessions)];
        }
        elseif (is_null($sessions[array_key_last($sessions)]->getFormationSessionDate()))
        {
            $data['Session'] = $sessions[array_key_last($sessions)];
        }
        else
        {
            $data['Session'] = new FormationSession();

            $data['Session']->setFormationSessionType($type);

            $entityManager->persist($data['Session']);
            $entityManager->flush();
        }

        $candidate = new FormationSessionCandidate();

        $candidate->setFormationSessionCandidateDate(new DateTime());
        $candidate->setFormationSessionCandidateSession($data['Session']);

        $form = $this->createForm(FormationType::class, $candidate, array('formData' => array('Form' => 'Subscription', 'formType' => $formType), 'data_class' => FormationSessionCandidate::class, 'action' => $this->generateUrl('formation-subscription', array('type' => $type, 'formType' => $formType))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if ($formType == 1)
            {
                $member = $doctrine->getRepository(Member::class)->findOneBy(array('memberId' => $form->get('FormationSessionCandidateMember')->getData()));

                if (!is_null($member))
                {
                    $check = false;

                    if (strtolower($member->getMemberFirstname()) == strtolower($form->get('FormationSessionCandidateFirstname')->getData()))
                    {
                        $check = true;

                        $member->setMemberEmail($form->get('FormationSessionCandidateEmail')->getData());
                    }

                    if ($check && (strtolower($member->getMemberName()) != strtolower($form->get('FormationSessionCandidateName')->getData())))
                    {
                        $this->addFlash('warning', 'Le numéro de licence ne correspond pas au nom et prénom indiquer.  Réessayer ou prenez contact avec votre responsable de club.' );

                        return $this->redirectToRoute('formation-subscription', array('type' => $type));
                    }
                    else
                    {
                        $member->setMemberEmail($form->get('FormationSessionCandidateEmail')->getData());

                        $candidate->setFormationSessionCandidateMember($member);
                        $candidate->setFormationSessionCandidateEmail();
                    }
                }
            }

            $entityManager->persist($candidate);
            $entityManager->flush();

            $this->addFlash('success', 'Votre inscription est bien enregistrée.' );

            return $this->redirectToRoute('formation-subscription', array('type' => $type));
        }

        return $this->render('/Formation/subscription.html.twig', array('form' => $form->createView(), 'data' => $data));
    }

    /**
     * @param Access $access
     * @param FormationSession $formationSession
     * @return Response
     */
    #[Route('/details-de-la-session/{formationSession<\d+>}', name:'index')]
    public function index(Access $access, FormationSession $formationSession): Response
    {
        if (!$access->check('Formation-SessionManagement'))
        {
            die();
        }

        $data['Session'] = $formationSession;

        return $this->render('Formation/index.html.twig', array('data' => $data));
    }

    /**
     * @param Access                    $access
     * @param FormationSession          $formationSession
     * @param FormationSessionCandidate $formationSessionCandidate
     * @param EntityManagerInterface    $em
     * @param Request                   $request
     *
     * @return Response
     */
    #[Route('/details-de-la-session/{formationSession<\d+>}/candidat/{formationSessionCandidate<\d+>}', name:'candidateDetails')]
    public function candidateDetails(Access $access, FormationSession $formationSession, FormationSessionCandidate $formationSessionCandidate, EntityManagerInterface $em, Request $request): Response
    {
        if (!$access->check('Formation-SessionManagement'))
        {
            die();
        }

        $data['Candidate'] = $formationSessionCandidate;
        $data['Session']   = $formationSession;

        $formDelete = $this->createForm(FormationType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('formation-candidateDetails', array('formationSession' => $formationSession->getFormationSessionId(), 'formationSessionCandidate' => $formationSessionCandidate->getFormationSessionCandidateId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $em->remove($formationSessionCandidate);
            $em->flush();

            return $this->redirectToRoute('formation-index', array('formationSession' => $formationSession->getFormationSessionId()));
        }

        return $this->render('Formation/Modal/candidateDetails.html.twig', array('data' => $data, 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access           $access
     * @param FormationSession $formationSession
     *
     * @return Response
     */
    #[Route('/details-de-la-session/{formationSession<\d+>}/liste-csv', name:'csvList')]
    public function csvList(Access $access, FormationSession $formationSession): Response
    {
        if (!$access->check('Formation-SessionManagement'))
        {
            die();
        }

        $candidates = $formationSession->getFormationSessionCandidates();

        $file = fopen('List.csv', 'w');

        fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($file, ['Prénom', 'Nom', 'Sexe', 'Date de naissance', '# National', 'Nationalité', 'Adresse', 'Code Postal', 'Localité', 'Pays', 'Téléphone', 'Email'], ";");

        foreach ($candidates as $candidate)
        {
            $entry['Firstname']        = $candidate->getFormationSessionCandidateFirstname();
            $entry['Name']             = $candidate->getFormationSessionCandidateName();
            $entry['Sex']              = $candidate->getFormationSessionCandidateSex(true);
            $entry['Birthday']         = $candidate->getFormationSessionCandidateBirthday(true);
            $entry['NationalID']       = 'Inconnu';
            $entry['Nationality']      = 'Inconnue';
            $entry['Address']          = $candidate->getFormationSessionCandidateAddress();
            $entry['Zip']              = $candidate->getFormationSessionCandidateZip();
            $entry['City']             = $candidate->getFormationSessionCandidateCity();
            $entry['Country']          = $candidate->getFormationSessionCandidateCountry(true);
            $entry['Phone']            = $candidate->getFormationSessionCandidatePhone();
            $entry['Email']            = $candidate->getFormationSessionCandidateEmail();

            fputcsv($file, $entry, ";");
        }

        fclose($file);

        $stream = new Stream('List.csv');

        $response = new BinaryFileResponse($stream);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export.csv');

        return $response->deleteFileAfterSend();
    }
}
