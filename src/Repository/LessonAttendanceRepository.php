<?php
// src/Repository/LessonAttendanceRepositorytory.php
namespace App\Repository;

use App\Entity\LessonAttendance;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LessonAttendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method LessonAttendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method LessonAttendance[]    findAll()
 * @method LessonAttendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonAttendanceRepository extends ServiceEntityRepository
{
    /**
     * LessonAttendanceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LessonAttendance::class);
    }
}
