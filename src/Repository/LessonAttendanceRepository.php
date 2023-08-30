<?php
// src/Repository/LessonAttendanceRepositorytory.php
namespace App\Repository;

use App\Entity\Lesson;
use App\Entity\LessonAttendance;

use App\Entity\Member;
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

    /**
     * @param array $ids
     * @return array
     */
    public function getLastLesson(array $ids): array
    {
        $qb = $this->createQueryBuilder('la');

        $qb->select('m.member_id AS Id', 'max(l.lesson_date) AS Last')
            ->innerJoin(Lesson::class, 'l', 'WITH', $qb->expr()->eq('l.lesson_id', 'la.lesson'))
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('la.lesson_attendance_member', 'm.member_id'));

        foreach ($ids as $id)
        {
            $qb->orWhere($qb->expr()->eq('la.lesson_attendance_member', $id));
        }

        $qb->groupBy('la.lesson_attendance_member');

        return $qb->groupBy('m.member_id')
            ->getQuery()
            ->getArrayResult();
    }
}
