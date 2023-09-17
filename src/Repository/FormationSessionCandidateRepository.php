<?php
// src/Repository/GradeSessionCandidateRepository.php
namespace App\Repository;

use App\Entity\FormationSessionCandidate;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FormationSessionCandidate|null find($id, $lockMode = null, $lockVersion = null)
 * @method FormationSessionCandidate|null findOneBy(array $criteria, array $orderBy = null)
 * @method FormationSessionCandidate[]    findAll()
 * @method FormationSessionCandidate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationSessionCandidateRepository extends ServiceEntityRepository
{
    /**
     * GradeSessionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FormationSessionCandidate::class);
    }
}
