<?php
// src/Repository/ClusterMemberRepository.php
namespace App\Repository;

use App\Entity\ClusterMember;

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
}
