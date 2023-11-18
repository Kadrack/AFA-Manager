<?php
// src/Repository/ClusterMemberRepository.php
namespace App\Repository;

use App\Entity\Cluster;
use App\Entity\ClusterMember;
use App\Entity\Member;

use DateTime;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClusterMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClusterMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClusterMember[]    findAll()
 * @method ClusterMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClusterMemberRepository extends ServiceEntityRepository
{
    /**
     * ClusterMemberRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClusterMember::class);
    }

    public function getFreeTrainingMember(DateTime $date): array
    {
        $qb = $this->createQueryBuilder('cm');

        return $qb->select('m.memberId as Id')
            ->distinct()
            ->innerJoin(member::class, 'm', 'WITH', $qb->expr()->eq('m.memberId', 'cm.clusterMember'))
            ->innerJoin(Cluster::class, 'c', 'WITH', $qb->expr()->eq('c.clusterId' , 'cm.clusterMemberCluster'))
            ->where($qb->expr()->eq('c.clusterFreeTraining', 1))
            ->andWhere($qb->expr()->lte('cm.clusterMemberDateIn', "'" . $date->format('Y-m-d') . "'"))
            ->andWhere($qb->expr()->orX($qb->expr()->gte('cm.clusterMemberDateOut', "'" . $date->format('Y-m-d') . "'"), $qb->expr()->isNull('cm.clusterMemberDateOut')))
            ->getQuery()
            ->getArrayResult();
    }
}
