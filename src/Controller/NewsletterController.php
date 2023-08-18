<?php
// src/Controller/CommonController.php
namespace App\Controller;

use App\Entity\Newsletter;
use App\Entity\NewsletterSubscription;

use App\Form\NewsletterType;

use App\Service\Access;

use DateTime;

use Doctrine\Persistence\ManagerRegistry;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class NewsletterController
 * @package App\Controller
 */
#[Route('/newsletter', name:'newsletter-')]
class NewsletterController extends AbstractController
{
    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/', name:'index')]
    public function index(Access $access, ManagerRegistry $doctrine): Response
    {
        $data['OnGoing']   = array();
        $data['Published'] = array();

        $list = $doctrine->getRepository(Newsletter::class)->findBy(array(), array('newsletter_date' => 'DESC'));

        foreach ($list as $news)
        {
            if (!is_null($news->getNewsletterDate()))
            {
                $data['Published'][] = $news;
            }
            elseif (is_null($news->getNewsletterDate()) && $access->check('News-FullList'))
            {
                $data['OnGoing'][] = $news;
            }
        }

        return $this->render('Newsletter/index.html.twig', array('data' => $data));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @return Response
     */
    #[Route('/creer', name:'create')]
    public function create(Access $access, ManagerRegistry $doctrine, Request $request): Response
    {
        if (!$access->check('News-Create'))
        {
            die();
        }

        $newsletter = new Newsletter();

        $form = $this->createForm(NewsletterType::class, $newsletter, array('formData' => array('Form' => 'Create'), 'data_class' => Newsletter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->persist($newsletter);
            $entityManager->flush();

            return $this->redirectToRoute('newsletter-view', array('newsletter' => $newsletter->getNewsletterId()));
        }

        return $this->render('Newsletter/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Newsletter $newsletter
     * @return Response
     */
    #[Route('/voir/{newsletter<\d+>}', name:'view')]
    public function view(Newsletter $newsletter): Response
    {
        $newsletter->setNewsletterView($this->renderView('Newsletter/template.html.twig', array('data' => $newsletter)));

        return $this->render('Newsletter/view.html.twig', array('data' => $newsletter));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Newsletter $newsletter
     * @param Request $request
     * @return Response
     */
    #[Route('/modifier/{newsletter<\d+>}', name:'edit')]
    public function edit(Access $access, ManagerRegistry $doctrine, Newsletter $newsletter, Request $request): Response
    {
        if (!$access->check('News-Edit') || !is_null($newsletter->getNewsletterDate()))
        {
            die();
        }

        $form = $this->createForm(NewsletterType::class, $newsletter, array('formData' => array('Form' => 'Create'), 'data_class' => Newsletter::class));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            return $this->redirectToRoute('newsletter-view', array('newsletter' => $newsletter->getNewsletterId()));
        }

        return $this->render('Newsletter/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Access $access
     * @param ManagerRegistry $doctrine
     * @param Newsletter $newsletter
     * @return Response
     */
    #[Route('/supprimer/{newsletter<\d+>}', name:'delete')]
    public function delete(Access $access, ManagerRegistry $doctrine, Newsletter $newsletter): Response
    {
        if (!$access->check('News-Delete') || !is_null($newsletter->getNewsletterDate()))
        {
            die();
        }

        $entityManager = $doctrine->getManager();

        $entityManager->remove($newsletter);
        $entityManager->flush();

        return $this->redirectToRoute('newsletter-index');
    }

    /**
     * @param Access $access
     * @param MailerInterface $mailer
     * @param ManagerRegistry $doctrine
     * @param Newsletter $newsletter
     * @param int $id
     * @return Response
     * @throws TransportExceptionInterface
     */
    #[Route('/envoyer/{newsletter<\d+>}/{id<\d+>}', name:'send')]
    public function send(Access $access, MailerInterface $mailer, ManagerRegistry $doctrine, Newsletter $newsletter, int $id = 1): Response
    {
        if (!$access->check('News-Send') || !is_null($newsletter->getNewsletterDate()))
        {
            die();
        }

        $addresses = $doctrine->getRepository(NewsletterSubscription::class)->getNewsletterSubscriptions($id);

        if (sizeof($addresses) == 0)
        {
            $newsletter->setNewsletterDate(new DateTime());
            $newsletter->setNewsletterView($this->renderView('Newsletter/template.html.twig', array('data' => $newsletter)));

            $entityManager = $doctrine->getManager();

            $entityManager->flush();

            $this->addFlash('success', 'La newsletter a été envoyé');

            return $this->redirectToRoute('newsletter-view', array('newsletter' => $newsletter->getNewsletterId()));
        }

        foreach ($addresses as $address)
        {
            $email = (new TemplatedEmail())
                ->from(new Address('afa@aikido.be', 'Newsletter AFA'))
                ->to($address->getNewsletterSubscriptionEmail())
                ->subject($newsletter->getNewsletterTitle())
                ->htmlTemplate('Newsletter/template.html.twig')
                ->context(array('data' => $newsletter, 'address' => $address));

            $email->getHeaders()->addTextHeader('X-Auto-Response-Suppress', 'OOF, DR, RN, NRN, AutoReply');

            $mailer->send($email);
        }

        return $this->redirectToRoute('newsletter-send', array('newsletter' => $newsletter->getNewsletterId(), 'id' => $id+100));
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param string $uniqueId
     * @return RedirectResponse
     */
    #[Route('/abonner/{uniqueId}/', name:'subscribe')]
    public function subscribe(ManagerRegistry $doctrine, Request $request, string $uniqueId): RedirectResponse
    {
        if ($uniqueId == md5('AFA-Manager'))
        {
            if (is_null($doctrine->getRepository(NewsletterSubscription::class)->findOneBy(array('newsletter_subscription_email' => $request->get('email')))))
            {
                if ($request->get('salt') == md5($request->get('email').'Wordpress'))
                {
                    $subscription = new NewsletterSubscription();

                    $subscription->setNewsletterSubscriptionEmail($request->get('email'));

                    $entityManager = $doctrine->getManager();

                    $entityManager->persist($subscription);
                    $entityManager->flush();

                    $this->addFlash('success', 'Merci de votre inscriptions.  En attendant la prochaine vous pouvez consulter les anciennes newsletters.');
                }
            }
            else
            {
                $this->addFlash('warning', 'Vous êtiez déjà inscrit. Nous vous invitons à consulter les anciennes newsletters.');
            }
        }

        return $this->redirectToRoute('newsletter-index');
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param Newsletter $newsletter
     * @param NewsletterSubscription $subscription
     * @param string $uniqueId
     * @return RedirectResponse
     */
    #[Route('/{newsletter<\d+>}/desabonner/{subscription<\d+>}/{uniqueId}', name:'unsubscribe')]
    public function unsubscribe(ManagerRegistry $doctrine, Newsletter $newsletter, NewsletterSubscription $subscription, string $uniqueId): RedirectResponse
    {
        if ($subscription->getNewsletterSubscriptionUniqueId() == $uniqueId)
        {
            $entityManager = $doctrine->getManager();

            $entityManager->remove($subscription);
            $entityManager->flush();

            $this->addFlash('warning', 'Vous êtes maintenant désinscrit de la newsletter');
        }

        return $this->redirectToRoute('newsletter-view', array('newsletter' => $newsletter->getNewsletterId()));
     }
}
