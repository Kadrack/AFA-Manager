<?php
// src/Repository/ClubTeacherRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubHistory;
use App\Entity\ClubTeacher;
use App\Entity\Grade;
use App\Entity\Member;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClubTeacher|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubTeacher|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubTeacher[]    findAll()
 * @method ClubTeacher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubTeacherRepository extends ServiceEntityRepository
{
    /**
     * ClubTeacherRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubTeacher::class);
    }

    /**
     * @return array|null
     */
    public function getDojoChoStartingPractice(): ?array
    {
        $qb = $this->createQueryBuilder('t');

        return $qb->select('m.member_firstname AS Firstname', 'm.member_name AS Name', 'c.club_name AS ClubName', 'm.member_start_practice AS Starting')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 't.club_teacher_member'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('c.club_id', 't.club_teacher'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'c.club_id'))
            ->where($qb->expr()->eq('t.club_teacher_title', 1))
            ->andWhere($qb->expr()->eq('h.club_history_status', 1))
            ->having($qb->expr()->eq('max(h.club_history_status)', 1))
            ->groupBy('c.club_id')
            ->orderBy('Firstname', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int|null $club
     * @param bool $mail
     * @return array|null
     */
    public function getDojoCho(?int $club, bool $mail = false): ?array
    {
        $qb = $this->createQueryBuilder('t');

        $qb->select('m')
            ->leftJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 't.club_teacher_member'))
            ->leftJoin(Grade::class, 'g', 'WITH', $qb->expr()->eq('m.member_id', 'g.grade_member'))
            ->innerJoin(Club::class, 'c', 'WITH', $qb->expr()->eq('c.club_id', 't.club_teacher'))
            ->where($qb->expr()->eq('t.club_teacher_title', 1));

        if (!is_null($club))
        {
            $qb->andWhere($qb->expr()->eq('t.club_teacher', $club));
        }

        if ($mail)
        {
            $qb->andWhere($qb->expr()->isNotNull('m.member_email'));
        }

        return $qb->groupBy('m.member_id')
            ->orderBy('m.member_firstname', 'ASC')
            ->addOrderBy('m.member_name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
