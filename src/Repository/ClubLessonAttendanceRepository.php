<?php
// src/Repository/LessonAttendanceRepositorytory.php
namespace App\Repository;

use App\Entity\ClubLesson;
use App\Entity\ClubLessonAttendance;

use App\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClubLessonAttendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubLessonAttendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubLessonAttendance[]    findAll()
 * @method ClubLessonAttendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubLessonAttendanceRepository extends ServiceEntityRepository
{
    /**
     * LessonAttendanceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubLessonAttendance::class);
    }

    /**
     * @param array $ids
     * @return array
     */
    public function getLastLesson(array $ids): array
    {
        $qb = $this->createQueryBuilder('la');

        $qb->select('m.memberId AS Id', 'max(l.clubLessonDate) AS Last')
            ->innerJoin(ClubLesson::class, 'l', 'WITH', $qb->expr()->eq('l.clubLessonId', 'la.clubLessonAttendanceClubLesson'))
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('la.clubLessonAttendanceMember', 'm.memberId'));

        foreach ($ids as $id)
        {
            $qb->orWhere($qb->expr()->eq('la.clubLessonAttendanceMember', $id));
        }

        $qb->groupBy('la.clubLessonAttendanceMember');

        return $qb->groupBy('m.memberId')
            ->getQuery()
            ->getArrayResult();
    }
}
