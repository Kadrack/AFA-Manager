<?php
// src/EventListener/EntityClubCluster.php
namespace App\EventListener;

use App\Entity\ClusterMember;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;

use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;

use Doctrine\ORM\Events;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsEntityListener(event: Events::postPersist, method: 'clusterMember', entity: ClusterMember::class)]
#[AsEntityListener(event: Events::postRemove, method: 'clusterMember', entity: ClusterMember::class)]
#[AsEntityListener(event: Events::postUpdate, method: 'clusterMember', entity: ClusterMember::class)]
class EntityClusterMember
{
    private ParameterBagInterface $parameters;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->parameters = $parameters;
    }

    public function clusterMember(ClusterMember $clusterMember, PostPersistEventArgs|PostRemoveEventArgs|PostUpdateEventArgs $event): void
    {
        $clusterMembers = json_decode(file_get_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/composition.json'), true);

        $list = array(1, 2, 3, 4, 5, 6, 9, 10);

        $id = $clusterMember->getClusterMemberCluster()->getClusterId();

        if (in_array($id, $list))
        {
            $members = array();

            foreach ($clusterMember->getClusterMemberCluster()->getClusterActiveMembers() as $member)
            {
                if ($member->getClusterMemberTitle(true) == 'Président(e)')
                {
                    $number = 1;
                }
                elseif ($member->getClusterMemberTitle(true) == 'Vice-Président(e)')
                {
                    $number = 2;
                }
                elseif ($member->getClusterMemberTitle(true) == 'Secrétaire' || $member->getClusterMemberTitle(true) == 'Secrétaire général(e)')
                {
                    $number = 3;
                }
                elseif ($member->getClusterMemberTitle(true) == 'Trésorier(ère) général(e)')
                {
                    $number = 4;
                }
                else
                {
                    $number = 5;
                }

                $members[] = array(
                    'Firstname'   => $member->getClusterMemberFirstname(),
                    'Name'        => $member->getClusterMemberName(),
                    'Title'       => $member->getClusterMemberTitle(true),
                    'TitleNumber' => $number,
                    'Email'       => $id == 9 ? null : $member->getClusterMemberEmail(),
                    'Phone'       => $id == 9 ? null : $member->getClusterMember()?->getMemberPhone()
                );
            }

            $clusterMembers[$id] = array('GroupName' => $clusterMember->getClusterMemberCluster()->getClusterName(), 'Members' => $members);
        }

        if ($this->parameters->get('kernel.environment') == 'prod')
        {
            file_put_contents('/home/fzns3054/aikido.be/wordpress/prod/wp-content/uploads/json/composition.json', json_encode($clusterMembers));
        }
    }
}
