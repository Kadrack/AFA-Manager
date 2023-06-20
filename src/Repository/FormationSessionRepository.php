<?php
// src/Repository/FormationSessionRepository.php
namespace App\Repository;

use App\Entity\FormationSession;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormationSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormationSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormationSession[]    findAll()
 * @method FormationSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationSessionRepository extends ServiceEntityRepository
{
    /**
     * GradeTitleRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormationSession::class);
    }
}
