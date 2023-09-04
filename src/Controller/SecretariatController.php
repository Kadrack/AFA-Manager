<?php
// src/Controller/SecretariatController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClubTeacher;
use App\Entity\Cluster;
use App\Entity\ClusterMember;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\Title;
use App\Entity\User;

use App\Form\CommonType;
use App\Form\SecretariatType;

use App\Service\Access;
use App\Service\EmailSender;
use App\Service\ListData;

use DateTime;

use Doctrine\Persistence\ManagerRegistry;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Stream;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SecretariatController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
#[Route('/secretariat', name:'secretariat-')]
class SecretariatController extends AbstractController
{
    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/acces-non-membres', name:'loginIndex')]
    public function loginIndex(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('Admin-Login'))
        {
            die();
        }

        $data['List'] = $doctrine->getRepository(User::class)->findBy(['user_member' => null], ['login' => 'ASC']);

        return $this->render('Secretariat/Login/index.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/ajouter-un-acces-non-membre', name:'loginAdd')]
    public function loginAdd(Access $access, ManagerRegistry $doctrine, Request $request): Response
    {
        if (!$access->check('Admin-Login'))
        {
            die();
        }

        $user = new User();

        $form = $this->createForm(SecretariatType::class, $user, array('formData' => array('Form' => 'Login', 'Action' => 'Add'), 'data_class' => User::class, 'action' => $this->generateUrl('secretariat-loginAdd')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword('Mdp');

            $entityManager = $doctrine->getManager();

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-loginIndex');
        }

        return $this->render('Secretariat/Login/Modal/add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param User $user
     * @return Response
     */
    #[Route('/acces-non-membre/{user<\d+>}/modifier', name:'loginEdit')]
    public function loginEdit(Access $access, ManagerRegistry $doctrine, Request $request, User $user): Response
    {
        if (!$access->check('Admin-Login'))
        {
            die();
        }

        $user = $doctrine->getRepository(User::class)->findOneBy(['id' => $user->getId()]);

        $formEdit = $this->createForm(SecretariatType::class, $user, array('formData' => array('Form' => 'Login', 'Action' => 'Edit'), 'data_class' => User::class, 'action' => $this->generateUrl('secretariat-loginEdit', array('user' => $user->getId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-loginIndex');
        }

        $formDelete = $this->createForm(SecretariatType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('secretariat-loginEdit', array('user' => $user->getId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($user);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-loginIndex');
        }

        return $this->render('Secretariat/Login/Modal/edit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-cluster', name:'clusterList')]
    public function clusterList(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('Admin-Cluster'))
        {
            die();
        }

        $data['Clusters'] = $doctrine->getRepository(Cluster::class)->findBy(array(), array('cluster_name' => 'ASC'));

        return $this->render('Secretariat/Cluster/list.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param Cluster $cluster
     * @return Response
     */
    #[Route('/detail-cluster/{cluster<\d+>}', name:'clusterIndex')]
    public function clusterIndex(Access $access, Cluster $cluster): Response
    {
        if (!$access->check('Admin-Cluster'))
        {
            die();
        }

        $data['Cluster'] = $cluster;

        return $this->render('Secretariat/Cluster/index.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/ajouter-cluster', name:'clusterAdd')]
    public function clusterAdd(Access $access, ManagerRegistry $doctrine, Request $request): RedirectResponse|Response
    {
        if (!$access->check('Admin-Cluster'))
        {
            die();
        }

        $cluster = new Cluster();

        $form = $this->createForm(SecretariatType::class, $cluster, array('formData' => array('Form' => 'Cluster', 'Action' => 'Add'), 'data_class' => Cluster::class, 'action' => $this->generateUrl('secretariat-clusterAdd')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->persist($cluster);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-clusterList');
        }

        return $this->render('Secretariat/Cluster/Modal/add.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param Cluster $cluster
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/modifier-cluster/{cluster<\d+>}', name:'clusterEdit')]
    public function clusterEdit(Access $access, Cluster $cluster, ManagerRegistry $doctrine, Request $request): RedirectResponse|Response
    {
        if (!$access->check('Admin-Cluster'))
        {
            die();
        }

        $formDelete = $this->createForm(SecretariatType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('secretariat-clusterEdit', array('cluster' => $cluster->getClusterId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($cluster);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-clusterList');
        }

        $formEdit = $this->createForm(SecretariatType::class, $cluster, array('formData' => array('Form' => 'Cluster', 'Action' => 'Edit'), 'data_class' => Cluster::class, 'action' => $this->generateUrl('secretariat-clusterEdit', array('cluster' => $cluster->getClusterId()))));

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-clusterIndex', array('cluster' => $cluster->getClusterId()));
        }

        return $this->render('Secretariat/Cluster/Modal/edit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param Cluster $cluster
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/cluster/{cluster<\d+>}/ajouter-membre', name:'clusterMemberAdd')]
    public function clusterMemberAdd(Access $access, Cluster $cluster, ManagerRegistry $doctrine, Request $request): RedirectResponse|Response
    {
        if (!$access->check('Admin-Cluster'))
        {
            die();
        }

        $data['Cluster'] = $cluster;

        $clusterMember = new ClusterMember();

        $form = $this->createForm(SecretariatType::class, $clusterMember, array('formData' => array('Form' => 'ClusterMember', 'Action' => 'Add', 'UseTitle' => $cluster->getClusterUseTitle(), 'UseEmail' => $cluster->getClusterUseEmail()), 'data_class' => ClusterMember::class, 'action' => $this->generateUrl('secretariat-clusterMemberAdd', array('cluster' => $cluster->getClusterId()))));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $clusterMember->setCluster($cluster);

            $member = $doctrine->getRepository(Member::class)->findOneBy(['member_id' => $form->get('ClusterMember')->getData()]);

            if (!is_null($member))
            {
                $clusterMember->setClusterMember($member);

                $entityManager->persist($clusterMember);
                $entityManager->flush();

                return $this->redirectToRoute('secretariat-clusterIndex', array('cluster' => $cluster->getClusterId()));
            }
            elseif (!is_null($form->get('ClusterMember')->getData()))
            {
                $this->addFlash('warning', 'Ce numéro licence n\'existe pas');
            }
            else
            {
                $user = $doctrine->getRepository(User::class)->findOneBy(['login' => $form->get('ClusterMemberUser')->getData()]);

                if (!is_null($user))
                {
                    $clusterMember->setClusterMemberUser($user);

                    $entityManager->persist($clusterMember);
                    $entityManager->flush();
                }
                elseif (!is_null($form->get('ClusterMemberFirstname')->getData()) || !is_null($form->get('ClusterMemberName')->getData()))
                {
                    $entityManager->persist($clusterMember);
                    $entityManager->flush();
                }
                else
                {
                    $this->addFlash('warning', 'Cet utilisateur n\'existe pas');
                }
            }

            return $this->redirectToRoute('secretariat-clusterIndex', array('cluster' => $cluster->getClusterId()));
        }

        return $this->render('Secretariat/Cluster/Modal/memberAdd.html.twig', array('form' => $form->createView(), 'data' => $data));
    }

    /**
     * @param Access $access
     * @param Cluster $cluster
     * @param ClusterMember $clusterMember
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/cluster/{cluster<\d+>}/modifier-membre/{clusterMember<\d+>}', name:'clusterMemberEdit')]
    public function clusterMemberEdit(Access $access, Cluster $cluster, ClusterMember $clusterMember, ManagerRegistry $doctrine, Request $request): RedirectResponse|Response
    {
        if (!$access->check('Admin-Cluster'))
        {
            die();
        }

        $formDelete = $this->createForm(SecretariatType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('secretariat-clusterMemberEdit', array('cluster' => $cluster->getClusterId(), 'clusterMember' => $clusterMember->getClusterMemberId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($clusterMember);
            $entityManager->flush();

            return $this->redirectToRoute('secretariat-clusterIndex', array('cluster' => $cluster->getClusterId()));
        }

        $formEdit = $this->createForm(SecretariatType::class, $clusterMember, array('formData' => array('Form' => 'ClusterMember', 'Action' => 'Edit', 'IsMember' => !is_null($clusterMember->getClusterMember()), 'IsUser' => !is_null($clusterMember->getClusterMemberUser()), 'UseTitle' => $cluster->getClusterUseTitle(), 'UseEmail' => $cluster->getClusterUseEmail()), 'data_class' => ClusterMember::class, 'action' => $this->generateUrl('secretariat-clusterMemberEdit', array('cluster' => $cluster->getClusterId(), 'clusterMember' => $clusterMember->getClusterMemberId()))));

        if (!is_null($clusterMember->getClusterMember()))
        {
            $formEdit->get('ClusterMember')->setData($clusterMember->getClusterMember()->getMemberId());
        }
        elseif (!is_null($clusterMember->getClusterMemberUser()))
        {
            $formEdit->get('ClusterMemberUser')->setData($clusterMember->getClusterMemberUser()->getLogin());
        }
        else
        {
            $formEdit->get('ClusterMemberName')->setData($clusterMember->getClusterMemberName());
            $formEdit->get('ClusterMemberFirstname')->setData($clusterMember->getClusterMemberFirstname());
        }

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            if (!is_null($clusterMember->getClusterMember()))
            {
                $member = $doctrine->getRepository(Member::class)->findOneBy(array('member_id' => $formEdit->get('ClusterMember')->getData()));

                if (is_null($member))
                {
                    $this->addFlash('warning', 'Ce numéro licence n\'existe pas');

                    return $this->redirectToRoute('secretariat-clusterIndex', array('cluster' => $cluster->getClusterId()));
                }
                else
                {
                    $clusterMember->setClusterMember($member);
                }
            }
            elseif (!is_null($clusterMember->getClusterMemberUser()))
            {
                $user = $doctrine->getRepository(User::class)->findOneBy(array('login' => $formEdit->get('ClusterMemberUser')->getData()));

                if (is_null($user))
                {
                    $this->addFlash('warning', 'Cet utilisateur n\'existe pas');

                    return $this->redirectToRoute('secretariat-clusterIndex', array('cluster' => $cluster->getClusterId()));
                }
                else
                {
                    $clusterMember->setClusterMemberUser($user);
                }
            }

            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-clusterIndex', array('cluster' => $cluster->getClusterId()));
        }

        return $this->render('Secretariat/Cluster/Modal/memberEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param EmailSender $emailSender
     * @param Request $request
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[Route('/mailing', name:'mailingIndex')]
    public function mailingIndex(Access $access, EmailSender $emailSender, Request $request): Response
    {
        if (!$access->check('Admin-Mailing'))
        {
            die();
        }

        $data['List'] = $emailSender->getMemberMailingList();

        $form = $this->createForm(CommonType::class, null, array('formData' => array('Form' => 'Mailing', 'Data' => $data)));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $email['Subject']   = $form['Subject']->getData();
            $email['Content']   = $form['Text']->getData();

            is_null($form['Attachment']->getData()) ?: $emailSender->setAttachment($form['Attachment']->getData());

            $emailSender->toMemberMailing($email, $form['To']->getData());

            return $this->redirectToRoute('secretariat-mailingIndex');
        }

        return $this->render('Secretariat/Mailing/index.html.twig', array('form' => $form->createView(), 'data' => $data));
    }

    /**
     * @param Access $access
     * @param int|null $list
     * @return Response
     */
    #[Route('/liste/{list<\d+>}', name:'listIndex')]
    public function listIndex(Access $access, ?int $list = null): Response
    {
        if (!$access->check('Admin-List'))
        {
            die();
        }

        $data = array();

        switch ($list)
        {
            case null :
                !$access->check('List-Licence') ?: $data['List'][1]  = 'Licence - En attente de paiement';
                !$access->check('List-Licence') ?: $data['List'][2]  = 'Licence - Derniers paiements';
                !$access->check('List-Licence') ?: $data['List'][3]  = 'Licence - Encodage formulaire';
                !$access->check('List-Licence') ?: $data['List'][4]  = 'Licence - Dernier timbre imprimé';
                !$access->check('List-Licence') ?: $data['List'][5]  = 'Licence - Timbres à imprimer';
                !$access->check('List-Licence') ?: $data['List'][6]  = 'Licence - Pass Sport';
                !$access->check('List-Licence') ?: $data['List'][7]  = 'Licence - Dernières licences expirées';
                !$access->check('List-Various') ?: $data['List'][8]  = 'Date de création des clubs';
                !$access->check('List-Various') ?: $data['List'][9]  = 'Temps de pratique Dojo-Cho';
                !$access->check('List-Various') ?: $data['List'][10] = 'Détails club pour FWB';
                !$access->check('List-Various') ?: $data['List'][11] = 'Liste des Shihan';
                !$access->check('List-Various') ?: $data['List'][12] = 'Listes pour IAF';
                !$access->check('List-Various') ?: $data['List'][13] = 'Professeurs enfants';

                break;
            case 1  : return $this->redirectToRoute('secretariat-paymentOnGoing');
            case 2  : return $this->redirectToRoute('secretariat-paymentLast');
            case 3  : return $this->redirectToRoute('secretariat-encodedFormList');
            case 4  : return $this->redirectToRoute('secretariat-printedStampList');
            case 5  : return $this->redirectToRoute('secretariat-stampsToPrint');
            case 6  : return $this->redirectToRoute('secretariat-passSport');
            case 7  : return $this->redirectToRoute('secretariat-lastExpiredList');
            case 8  : return $this->redirectToRoute('secretariat-creationClubList');
            case 9  : return $this->redirectToRoute('secretariat-dojoChoStartingPractice');
            case 10 : return $this->redirectToRoute('secretariat-clubsDetailsFWB');
            case 11 : return $this->redirectToRoute('secretariat-shihanList');
            case 12 : return $this->redirectToRoute('secretariat-iafList');
            case 13 : return $this->redirectToRoute('secretariat-childTeacherList');
        }

        return $this->render('Secretariat/List/index.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-en-attente-de-paiement', name:'paymentOnGoing')]
    public function paymentOnGoing(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Licence'))
        {
            die();
        }

        $list = array();

        $licences = $doctrine->getManager()->getRepository(MemberLicence::class)->getPaymentOnGoing();

        foreach ($licences as $licence)
        {
            $list[$licence->getMemberLicenceClub()->getClubId()][]= $licence;
        }

        return $this->render('Secretariat/Lists/ongoingLicences.html.twig', array('list' => $list));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-derniers-paiement', name:'paymentLast')]
    public function paymentLast(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Licence'))
        {
            die();
        }

        $data['List'] = $doctrine->getRepository(Member::class)->getPaymentList();

        return $this->render('Secretariat/Payment/list.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param MemberLicence $licence
     * @param Request $request
     * @return Response
     */
    #[Route('/edition-paiement/{licence<\d+>}/', name:'paymentEdit')]
    public function paymentEdit(Access $access, ManagerRegistry $doctrine, MemberLicence $licence, Request $request): Response
    {
        if (!$access->check('List-Licence'))
        {
            die();
        }

        $formEdit = $this->createForm(SecretariatType::class, $licence, array('formData' => array('Form' => 'Payment', 'Action' => 'Edit'), 'data_class' => MemberLicence::class, 'action' => $this->generateUrl('secretariat-paymentEdit', array('licence' => $licence->getMemberLicenceId()))));

        $formEdit->get('MemberId')->setData($licence->getMemberLicence()->getMemberId());

        $formEdit->handleRequest($request);

        if ($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $member = $doctrine->getRepository(Member::class)->findOneBy(['member_id' => $formEdit->get('MemberId')->getData()]);

            if (!is_null($member))
            {
                $licence->setMemberLicence($member);

                $entityManager = $doctrine->getManager();

                $entityManager->flush();
            }

            return $this->redirectToRoute('secretariat-paymentList');
        }

        $formDelete = $this->createForm(SecretariatType::class, null, array('formData' => array('Form' => 'Delete'), 'action' => $this->generateUrl('secretariat-paymentEdit', array('licence' => $licence->getMemberLicenceId()))));

        $formDelete->handleRequest($request);

        if ($formDelete->isSubmitted() && $formDelete->isValid())
        {
            $licence->setMemberLicencePaymentDate(null);
            $licence->setMemberLicencePaymentUpdate(new DateTime());
            $licence->setMemberLicencePrintoutDone(null);
            $licence->setMemberLicencePrintoutCreation(null);

            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('secretariat-paymentList');
        }

        return $this->render('Secretariat/Payment/modalPaymentEdit.html.twig', array('formEdit' => $formEdit->createView(), 'formDelete' => $formDelete->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-encodage-formulaires/', name:'encodedFormList')]
    public function encodedFormList(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Licence'))
        {
            die();
        }

        $list = $doctrine->getManager()->getRepository(MemberLicence::class)->getLastEncodedForm();

        return $this->render('Secretariat/Lists/encodedForm.html.twig', array('list' => $list));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-impression-timbres/', name:'printedStampList')]
    public function printedStampedList(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Licence'))
        {
            die();
        }

        $list = $doctrine->getManager()->getRepository(MemberLicence::class)->getLastPrintedStamp();

        return $this->render('Secretariat/Lists/printedStamp.html.twig', array('list' => $list));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-des-timbres-a-imprimer', name:'stampsToPrint')]
    public function stampsToPrint(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Licence'))
        {
            die();
        }

        $list = array();

        $stamps = $doctrine->getRepository(MemberLicence::class)->getOnGoingStampLicence();

        foreach ($stamps as $stamp)
        {
            $list[$stamp->getMemberLicenceClub()->getClubId()][]= $stamp;
        }

        return $this->render('Secretariat/Lists/stampsToPrint.html.twig', array('list' => $list));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-des-pass-sport', name:'passSport')]
    public function passSport(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Licence'))
        {
            die();
        }

        $list= $doctrine->getRepository(Member::class)->getPassSportList();

        return $this->render('Secretariat/Lists/passSport.html.twig', array('list' => $list));
    }

    /**
     * @param Access $access
     * @param ListData $listData
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/telechargement-liste-des-pass-sport', name:'passSportDownload')]
    public function passSportDownload(Access $access, ListData $listData, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Licence'))
        {
            die();
        }

        $list = $doctrine->getRepository(Member::class)->getPassSportListDownload();

        $file = fopen('../private/export.csv', 'w');

        fwrite($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        $value['Count'] = 0;

        foreach ($list as $entry)
        {
            $value['Count'] = $value['Count'] + 1;
            $value['Start'] = $entry['Start']->format('d-m-Y');
            $value['Birthday'] = $entry['Birthday']->format('d-m-Y');
            $value['Sex'] = $listData->getSex($entry['Sex']);
            $value['Club'] = $entry['Club'];
            $value['Zip'] = $entry['Zip'];

            fputcsv($file, $value, "\t");
        }

        fclose($file);

        $stream = new Stream('../private/export.csv');

        $response = new BinaryFileResponse($stream);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'export.csv');

        return $response->deleteFileAfterSend();
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-date-creation-club', name:'creationClubList')]
    public function creationClubList(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Various'))
        {
            die();
        }

        $list = $doctrine->getRepository(Club::class)->getCreationDateList();

        return $this->render('Secretariat/Lists/clubCreationDate.html.twig', array('list' => $list));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-anniversaire-pratique-dojo-cho', name:'dojoChoStartingPractice')]
    public function dojoChoStartingPractice(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Various'))
        {
            die();
        }

        $list = $doctrine->getRepository(ClubTeacher::class)->getDojoChoStartingPractice();

        return $this->render('Secretariat/Lists/dojoChoStartingPractice.html.twig', array('list' => $list));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-federation-wallonie-bruxelles', name:'clubsDetailsFWB')]
    public function clubsDetailsFWB(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Various'))
        {
            die();
        }

        $list = $doctrine->getRepository(Club::class)->getClubsDetailsFWB();

        foreach ($list as $club)
        {
            if (isset($data[$club['Id']]))
            {
                $data[$club['Id']]['Dojo'][$club['DojoId']] = $club['AddressDojo'] . '<br />' . $club['ZipDojo'] . ' ' . ucwords(strtolower($club['CityDojo']));

                if (!is_null($club['Formation']))
                {
                    $data[$club['Id']]['Teacher'][$club['TeacherId']] = $club['TeacherFirstname'] . ' ' . $club['TeacherName'];
                }
            }
            else
            {
                $club['Dojo']    = array();
                $club['Teacher'] = array();

                $club['Address'] = $club['Address'] . '<br />' . $club['Zip'] . ' ' . ucwords(strtolower($club['City']));
                $club['Dojo'][$club['DojoId']] = $club['AddressDojo'] . '<br />' . $club['ZipDojo'] . ' ' . ucwords(strtolower($club['CityDojo']));

                if (!is_null($club['Formation']))
                {
                    $club['Teacher'][$club['TeacherId']] = $club['TeacherFirstname'] . ' ' . $club['TeacherName'];
                }

                $data[$club['Id']] = $club;
            }
        }

        return $this->render('Secretariat/Lists/clubsDetailsFWB.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-des-shihan', name:'shihanList')]
    public function shihanList(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Various'))
        {
            die();
        }

        $list = $doctrine->getRepository(Title::class)->findBy(array('title_rank' => 3));

        foreach ($list as $shihan)
        {
            if (!$shihan->getTitleMember()->getMemberOutdate())
            {
                $data['Shihan'][] = $shihan->getTitleMember();
            }
        }

        return $this->render('Secretariat/Lists/shihan.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-IAF', name:'iafList')]
    public function iafList(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Various'))
        {
            die();
        }

        $data['List'] = $doctrine->getRepository(Member::class)->getIAFList();

        return $this->render('Secretariat/Lists/iaf.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-prof-enfants', name:'childTeacherList')]
    public function childTeacherList(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Various'))
        {
            die();
        }

        $data['List'] = $doctrine->getRepository(ClubTeacher::class)->findBy(array('club_teacher_type' => array(2, 3)));

        return $this->render('Secretariat/Lists/childTeacher.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/liste-licence-expiree', name:'lastExpiredList')]
    public function lastExpiredList(Access $access, ManagerRegistry $doctrine): Response
    {
        if (!$access->check('List-Licence'))
        {
            die();
        }

        $data['List'] = $doctrine->getRepository(Member::class)->getLastExpiredList();

        return $this->render('Secretariat/Lists/lastExpired.html.twig', array('data' => $data));
    }
}
