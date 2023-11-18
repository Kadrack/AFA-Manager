<?php
// src/Controller/LoginController.php
namespace App\Controller;

use App\Entity\Member;
use App\Entity\User;

use App\Form\UserType;

use App\Notifier\CustomLoginLinkNotification;

use Doctrine\Persistence\ManagerRegistry;

use LogicException;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Profiler\Profiler;

use Symfony\Component\Notifier\NotifierInterface;

use Symfony\Component\Notifier\Recipient\Recipient;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

/**
 * Class SecurityController
 * @package App\Controller
 */
class LoginController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param Profiler|null $profiler
     * @return Response
     */
    #[Route('/login', name:'login')]
    public function login(AuthenticationUtils $authenticationUtils, ?Profiler $profiler): Response
    {
        $profiler?->disable();

        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Security/login.html.twig', array('last_username' => $lastUsername, 'error' => $error));
    }

    /**
     * @return mixed
     */
    #[Route('/logout', name:'logout')]
    public function logout(): mixed
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @return mixed
     */
    #[Route('/login-link', name:'loginLinkCheck')]
    public function loginLinkCheck(): mixed
    {
        throw new LogicException('This code should never be reached');
    }

    /**
     * @param LoginLinkHandlerInterface $loginLinkHandler
     * @param ManagerRegistry $doctrine
     * @param NotifierInterface $notifier
     * @param ParameterBagInterface $parameters
     * @param Profiler|null $profiler
     * @param Request $request
     * @return Response
     */
    #[Route('/recuperation-mot-de-passe', name:'loginLink')]
    public function requestLoginLink(LoginLinkHandlerInterface $loginLinkHandler, ManagerRegistry $doctrine, NotifierInterface $notifier, ParameterBagInterface $parameters, ?Profiler $profiler, Request $request): Response
    {
        if ($parameters->get('kernel.environment') == 'dev')
        {
            return $this->redirectToRoute('login');
        }

        $profiler?->disable();

        $form = $this->createForm(UserType::class, null, array('form' => 'createLoginLink', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $login = $form->get('Login')->getData();

            $user = $doctrine->getRepository(User::class)->findOneBy(['login' => $login]);

            if (($user?->getEmail() == $form->get('Email')->getData()) || ($user?->getMember()?->getMemberEmail() == $form->get('Email')->getData()))
            {
                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

                $notification = new CustomLoginLinkNotification($loginLinkDetails, 'AFA-Manager - Récupération mot de passe');

                if (is_null($user->getMember()))
                {
                    $recipient = new Recipient($user->getEmail());
                }
                else
                {
                    $recipient = new Recipient($user->getMember()->getMemberEmail());
                }

                $notifier->send($notification, $recipient);

                return $this->redirectToRoute('login');
            }

            $this->addFlash('warning', 'Le login et l\'email ne correspond à aucun compte.' );

            return $this->render('Security/loginLink.html.twig', array('form' => $form->createView()));
        }

        return $this->render('Security/loginLink.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param LoginLinkHandlerInterface $loginLinkHandler
     * @param ManagerRegistry $doctrine
     * @param NotifierInterface $notifier
     * @param ParameterBagInterface $parameters
     * @param Profiler|null $profiler
     * @param Request $request
     * @return Response
     */
    #[Route('/creer-login', name:'loginCreate')]
    public function loginCreate(LoginLinkHandlerInterface $loginLinkHandler, ManagerRegistry $doctrine, NotifierInterface $notifier, ParameterBagInterface $parameters, ?Profiler $profiler, Request $request): Response
    {
        if ($parameters->get('kernel.environment') == 'dev')
        {
            return $this->redirectToRoute('login');
        }

        $profiler?->disable();

        $form = $this->createForm(UserType::class, null, array('form' => 'createLogin', 'data_class' => null));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $member = $doctrine->getRepository(Member::class)->findOneBy(['memberId' => $form->get('MemberId')->getData(), 'memberFirstname' => $form->get('Firstname')->getData(), 'memberName' => $form->get('Name')->getData(), 'memberEmail' => $form->get('Email')->getData()]);

            $user = $doctrine->getRepository(User::class)->findOneBy(['userMember' => $member?->getMemberId()]);

            if (!is_null($member) && is_null($user))
            {
                $user = new User();

                $user->setMember($member);

                $login = $member->getMemberFirstname().'-'.$member->getMemberId();

                $i = 1;

                while (!is_null($doctrine->getRepository(User::class)->findOneBy(['login' => $member->getMemberFirstname().$member->getMemberId()])))
                {
                    $login = $member->getMemberFirstname().$i.'-'.$member->getMemberId();

                    $i++;
                }

                $user->setLogin($login);
                $user->setPassword(microtime());

                $entityManager = $doctrine->getManager();

                $entityManager->persist($user);
                $entityManager->flush();

                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

                $notification = new CustomLoginLinkNotification($loginLinkDetails, 'AFA-Manager - Nouveau login : '.$user->getLogin());

                $recipient = new Recipient($user->getMember()->getMemberEmail());

                $notifier->send($notification, $recipient);

                return $this->redirectToRoute('login');
            }

            $this->addFlash('warning', 'Membre non trouvé.  Les données doivent correspondre à ce que nous avons dans la base de données.  En cas de problème persistant veuillez contacter le secrétariat' );

            return $this->render('Security/loginCreate.html.twig', array('form' => $form->createView()));
        }

        return $this->render('Security/loginCreate.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/modification-du-login', name:'loginEdit')]
    public function loginEdit(ManagerRegistry $doctrine, Request $request): RedirectResponse|Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user, array('form' => 'loginEdit', 'action' => $this->generateUrl('loginEdit')));

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            if ($form->isValid())
            {
                $duplicate = $doctrine->getRepository(User::class)->findOneBy(['login' => $form->get('Login')->getData()]);

                if (is_null($duplicate) && (strlen($form->get('Login')->getData()) > 3) && (strlen($form->get('Login')->getData()) <= 20))
                {
                    $user->setLogin($form->get('Login')->getData());

                    $entityManager = $doctrine->getManager();

                    $entityManager->flush();

                    $this->addFlash('success', 'Votre login est maintenant '.$user->getLogin());
                }
                else
                {
                    if (strlen($form->get('Login')->getData()) <= 3)
                    {
                        $this->addFlash('danger', 'Le login doit comporter plus de 3 caractères');
                    }
                    elseif (strlen($form->get('Login')->getData()) > 20)
                    {
                        $this->addFlash('danger', 'Le login doit comporter moins de 20 caractères');
                    }
                    elseif (!is_null($duplicate))
                    {
                        $this->addFlash('danger', $form->get('Login')->getData().' est déjà utiliser par un autre utilisateur.');
                    }
                }
            }
            else
            {
                foreach ($form->getErrors() as $error)
                {
                    $this->addFlash('danger', $error->getMessage());
                }
            }

            return $this->redirectToRoute('common-index');
        }

        return $this->render('Security/Modal/loginEdit.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return RedirectResponse|Response
     */
    #[Route('/modification-du-mot-de-passe', name:'passwordEdit')]
    public function passwordEdit(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): RedirectResponse|Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserType::class, $user, array('form' => 'passwordEdit', 'action' => $this->generateUrl('passwordEdit')));

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            if ($form->isValid())
            {
                $user->setPassword($passwordHasher->hashPassword($user, $form->get('Password')->getData()));

                $entityManager = $doctrine->getManager();

                $entityManager->flush();

                $this->addFlash('success', 'Votre nouveau de passe est enregistré');
            }
            else
            {
                $this->addFlash('danger', 'Les deux mots de passe doivent correspondre');
            }

            return $this->redirectToRoute('common-index');
        }

        return $this->render('Security/Modal/passwordEdit.html.twig', array('form' => $form->createView()));
    }
}
