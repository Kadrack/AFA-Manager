<?php
// src/Repository/ClubClassRepository.php
namespace App\Repository;

use App\Entity\ClubClass;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClubClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubClass[]    findAll()
 * @method ClubClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubClassRepository extends ServiceEntityRepository
{
    /**
     * ClubLessonRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubClass::class);
    }
}
