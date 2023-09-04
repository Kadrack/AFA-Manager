<?php
// src/Repository/LessonRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\Lesson;
use App\Entity\LessonAttendance;
use App\Entity\Member;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lesson[]    findAll()
 * @method Lesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LessonRepository extends ServiceEntityRepository
{
    /**
     * LessonRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }

    /**
     * @param Club $club
     * @param int $year
     * @return array|null
     */
    public function getLesson(Club $club, int $year): ?array
    {
        $start = $year     . '-08-01';
        $end   = $year + 1 . '-07-31';

        $qb = $this->createQueryBuilder('l');

        return $qb->select('l')
            ->where($qb->expr()->gte('l.lesson_date', "'".$start."'"))
            ->andWhere($qb->expr()->lte('l.lesson_date', "'".$end."'"))
            ->andWhere($qb->expr()->eq('l.lesson_club', $club->getClubId()))
            ->orderBy('l.lesson_date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Club $club
     * @param int $year
     * @return array|null
     */
    public function getSummary(Club $club, int $year): ?array
    {
        $start = $year     . '-08-01';
        $end   = $year + 1 . '-07-31';

        $qb = $this->createQueryBuilder('l');

        return $qb->select('m.member_id as Id', 'm.member_firstname as Firstname', 'm.member_name as Name', 'l.lesson_date as Date', 'l.lesson_duration as Duration', 'l.lesson_type as Type')
            ->innerJoin(LessonAttendance::class, 'la', 'WITH', $qb->expr()->eq('l.lesson_id', 'la.lesson'))
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('la.lesson_attendance_member', 'm.member_id'))
            ->where($qb->expr()->gte('l.lesson_date', "'".$start."'"))
            ->andWhere($qb->expr()->lte('l.lesson_date', "'".$end."'"))
            ->andWhere($qb->expr()->eq('l.lesson_club', $club->getClubId()))
            ->orderBy('Firstname', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param Member $member
     * @return array|null
     */
    public function getLessonCount(Member $member): ?array
    {
        $grades = $member->getMemberGrades();

        $qb = $this->createQueryBuilder('l');

        $sum['Adult'] = $qb->select('sum(l.lesson_duration) As SumAdult')
            ->innerJoin(LessonAttendance::class, 'a', 'WITH', $qb->expr()->eq('l.lesson_id', 'a.lesson'))
            ->where($qb->expr()->eq('l.lesson_type', 1))
            ->andWhere($qb->expr()->gte('l.lesson_date', "'".$grades[0]->getGradeDate()?->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->eq('a.lesson_attendance_member', $member->getMemberId()))
            ->getQuery()
            ->getSingleResult();

        $qb = $this->createQueryBuilder('l');

        $sum['Child'] = $qb->select('sum(l.lesson_duration) As SumChild')
            ->innerJoin(LessonAttendance::class, 'a', 'WITH', $qb->expr()->eq('l.lesson_id', 'a.lesson'))
            ->where($qb->expr()->eq('l.lesson_type', 2))
            ->andWhere($qb->expr()->gte('l.lesson_date', "'".$grades[0]->getGradeDate()?->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->eq('a.lesson_attendance_member', $member->getMemberId()))
            ->getQuery()
            ->getSingleResult();

        return $sum;
    }
}
