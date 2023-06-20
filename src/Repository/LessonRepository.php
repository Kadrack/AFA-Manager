<?php
// src/Repository/LessonRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\Lesson;
use App\Entity\LessonAttendance;
use App\Entity\Member;

use DateInterval;
use DateTime;

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
     * @param DateTime|null $date
     * @return array|null
     */
    public function getLesson(Club $club, ?DateTime $date = null): ?array
    {
        if (is_null($date))
        {
            $date = new DateTime();
        }

        $limitLow  = date_sub(clone $date, DateInterval::createFromDateString('7 day'));
        $limitHigh = date_add(clone $date, DateInterval::createFromDateString('7 day'));

        $qb = $this->createQueryBuilder('l');

        return $qb->select('l')
            ->where($qb->expr()->gte('l.lesson_date', "'".$limitLow->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.lesson_date', "'".$limitHigh->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->eq('l.lesson_club', $club->getClubId()))
            ->orderBy('l.lesson_date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Member $member
     * @return array|null
     */
    public function getLessonCount(Member $member): ?array
    {
        $today = new DateTime();

        $grades = $member->getMemberGrades();

        $qb = $this->createQueryBuilder('l');

        return $qb->select('sum(l.lesson_duration) As Sum')
            ->innerJoin(LessonAttendance::class, 'a', 'WITH', $qb->expr()->eq('l.lesson_id', 'a.lesson'))
            ->where($qb->expr()->lte('l.lesson_date', "'".$today->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->gte('l.lesson_date', "'".$grades[0]->getGradeDate()->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->eq('a.lesson_attendance_member', $member->getMemberId()))
            ->getQuery()
            ->getArrayResult();
    }
}
