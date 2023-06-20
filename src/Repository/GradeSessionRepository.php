<?php
// src/Repository/GradeSessionRepository.php
namespace App\Repository;

use App\Entity\GradeSession;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GradeSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method GradeSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method GradeSession[]    findAll()
 * @method GradeSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GradeSessionRepository extends ServiceEntityRepository
{
    /**
     * GradeSessionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GradeSession::class);
    }
}
