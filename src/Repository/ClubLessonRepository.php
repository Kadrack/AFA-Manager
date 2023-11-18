<?php
// src/Repository/LessonRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubLesson;
use App\Entity\ClubLessonAttendance;
use App\Entity\Member;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\DBAL\Exception;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClubLesson|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubLesson|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubLesson[]    findAll()
 * @method ClubLesson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubLessonRepository extends ServiceEntityRepository
{
    /**
     * LessonRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubLesson::class);
    }

    /**
     * @param Club $club
     * @param int  $year
     *
     * @return array|null
     */
    public function getLesson(Club $club, int $year): ?array
    {
        $start = $year     . '-08-01';
        $end   = $year + 1 . '-07-31';

        $qb = $this->createQueryBuilder('l');

        return $qb->select('l')
            ->where($qb->expr()->gte('l.clubLessonDate', "'".$start."'"))
            ->andWhere($qb->expr()->lte('l.clubLessonDate', "'".$end."'"))
            ->andWhere($qb->expr()->eq('l.clubLessonClub', $club->getClubId()))
            ->orderBy('l.clubLessonDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Club $club
     * @param int  $year
     *
     * @return array|null
     */
    public function getSummary(Club $club, int $year): ?array
    {
        $start = $year     . '-08-01';
        $end   = $year + 1 . '-07-31';

        $qb = $this->createQueryBuilder('l');

        return $qb->select('m.memberId as Id', 'm.memberFirstname as Firstname', 'm.memberName as Name', 'l.clubLessonDate as Date', 'l.clubLessonDuration as Duration', 'l.clubLessonType as Type')
            ->innerJoin(ClubLessonAttendance::class, 'la', 'WITH', $qb->expr()->eq('l.clubLessonId', 'la.clubLessonAttendanceClubLesson'))
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('la.clubLessonAttendanceMember', 'm.memberId'))
            ->where($qb->expr()->gte('l.clubLessonDate', "'".$start."'"))
            ->andWhere($qb->expr()->lte('l.clubLessonDate', "'".$end."'"))
            ->andWhere($qb->expr()->eq('l.clubLessonClub', $club->getClubId()))
            ->orderBy('Firstname', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param Member $member
     *
     * @return array|null
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getLessonCount(Member $member): ?array
    {
        $grades = $member->getMemberGrades();

        $qb = $this->createQueryBuilder('l');

        $sum['Adult'] = $qb->select('sum(l.clubLessonDuration) As SumAdult')
            ->innerJoin(ClubLessonAttendance::class, 'a', 'WITH', $qb->expr()->eq('l.clubLessonId', 'a.clubLessonAttendanceClubLesson'))
            ->where($qb->expr()->eq('l.clubLessonType', 1))
            ->andWhere($qb->expr()->gte('l.clubLessonDate', "'".$grades[0]->getGradeDate()?->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->eq('a.clubLessonAttendanceMember', $member->getMemberId()))
            ->getQuery()
            ->getSingleResult();

        $qb = $this->createQueryBuilder('l');

        $sum['Child'] = $qb->select('sum(l.clubLessonDuration) As SumChild')
            ->innerJoin(ClubLessonAttendance::class, 'a', 'WITH', $qb->expr()->eq('l.clubLessonId', 'a.clubLessonAttendanceClubLesson'))
            ->where($qb->expr()->eq('l.clubLessonType', 2))
            ->andWhere($qb->expr()->gte('l.clubLessonDate', "'".$grades[0]->getGradeDate()?->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->eq('a.clubLessonAttendanceMember', $member->getMemberId()))
            ->getQuery()
            ->getSingleResult();

        return $sum;
    }

    /**
     * @param array $ids
     * @param int   $type
     *
     * @return array|null
     * @throws Exception
     */
    public function getLessonHourCount(array $ids, int $type): ?array
    {
        $query = 'SELECT m.memberId as Id, sum(l.clubLessonDuration) as Total
                    FROM clubLessonAttendance la
                    INNER JOIN clubLesson l ON l.clubLessonId = la.clubLessonAttendance_join_clubLesson 
                    INNER JOIN member m ON m.memberId = la.clubLessonAttendance_join_member
                    WHERE (';

        $start = true;

        foreach ($ids as $member)
        {
            if (is_null($member['GradeDate']))
            {
                continue;
            }

            $date = $member['GradeDate']->format('Y-m-d');

            if ($start)
            {
                $start = false;
            }
            else
            {
                $query = $query . " OR ";
            }

            $id = $member['Id'];

            $query = $query . "(m.memberId = $id AND l.clubLessonDate > '$date')";
        }

        $query = $query . ") AND l.clubLessonType = $type GROUP BY Id";

        return $this->getEntityManager()->getConnection()->executeQuery($query)->fetchAllAssociative();
    }
}
