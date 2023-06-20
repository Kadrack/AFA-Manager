<?php
// src/Controller/CommonController.php
namespace App\Controller;

use App\Entity\Club;
use App\Entity\ClusterMember;

use App\Service\Access;

use Doctrine\Persistence\ManagerRegistry;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommonController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
#[Route('', name:'common-')]
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
     * @param ManagerRegistry $doctrine
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route('/changer-le-type-acces/{id<\d+>}', name:'changeCluster')]
    public function changeCluster(Access $access, ManagerRegistry $doctrine, Request $request, int $id): Response
    {
        $club    = null;
        $cluster = null;

        if ($id < 1000)
        {
            $clusters = $doctrine->getRepository(ClusterMember::class)->findBy(array('cluster' => $id, 'cluster_member_user' => $this->getUser()->getId()));
            $clusters = array_merge($clusters, $doctrine->getRepository(ClusterMember::class)->findBy(array('cluster' => $id, 'cluster_member' => $this->getUser()?->getUserMember()->getMemberId())));

            foreach ($clusters as $clusterMember)
            {
                if ($clusterMember->getClusterMemberActive())
                {
                    $cluster = $clusterMember;
                }
            }
        }
        else
        {
            $club = $doctrine->getRepository(Club::class)->findOneBy(array('club_id' => $id));
        }

        $access->setListAccess($club, $cluster);

        return $this->redirect($request->headers->get('referer'));
    }
}
