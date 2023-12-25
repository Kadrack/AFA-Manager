<?php
// src/Controller/CommonController.php
namespace App\Controller;

use App\Entity\QrCodes;
use App\Repository\UserRepository;

use App\Service\Access;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\ExpressionLanguage\Expression;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class CommonController
 *
 * @package App\Controller
 */
#[Route('', name:'common-')]
#[IsGranted(new Expression('is_granted("ROLE_USER")'))]
class CommonController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route('/', name:'index')]
    public function index(): Response
    {
        return $this->render('Common/index.html.twig');
    }

    /**
     * @param Access $access
     * @param int    $id
     *
     * @return Response
     */
    #[Route('/changer-le-type-acces/{id<\d+>}', name:'changeCluster')]
    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    public function changeCluster(Access $access, int $id): Response
    {
        $access->setListAccess($id);

        return $this->redirectToRoute('common-index');
    }

    /**
     * @param EntityManagerInterface $em
     * @param Request                $request
     * @param UserRepository         $users
     * @param int|null               $id
     *
     * @return Response
     */
    #[Route('/modifier-le-theme/{id<\d+>}', name:'themeChange')]
    #[IsGranted(new Expression('is_granted("ROLE_USER")'))]
    public function themeChange(EntityManagerInterface $em, Request $request, UserRepository $users, ?int $id = null): Response
    {
        $themeList = array('cerulean', 'cosmo', 'cyborg', 'darkly', 'flatly', 'journal', 'litera', 'lumen', 'lux', 'materia', 'minty', 'morph', 'pulse', 'quartz', 'sandstone', 'simplex', 'sketchy', 'slate', 'solar', 'spacelab', 'superhero', 'united', 'vapor', 'yeti', 'zephyr');

        if (is_null($id))
        {
            return $this->render('Common/Modal/themeList.html.twig', array('themeList' => $themeList));
        }

        $user = $users->findOneBy(array('login' => $this->getUser()->getUserIdentifier()));

        $user->setTheme($themeList[$id-1]);

        $em->flush();

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @param QrCodes $qrCode
     *
     * @return Response
     */
    #[Route('/lecture-qr-codes/member/{qrCode}', name:'qrReadingMember')]
    public function qrReading(QrCodes $qrCode): Response
    {
        return $this->redirectToRoute('member-index', array('member' => $qrCode->getQrCodesMember()->getMemberId()));
    }
}
