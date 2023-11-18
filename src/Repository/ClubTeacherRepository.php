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

        return $qb->select('m.memberFirstname AS Firstname', 'm.memberName AS Name', 'c.clubName AS ClubName', 'm.memberStartPractice AS Starting')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.memberId', 't.clubTeacherMember'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('c.clubId', 't.clubTeacherClub'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.clubHistoryClub', 'c.clubId'))
            ->where($qb->expr()->eq('t.clubTeacherTitle', 1))
            ->andWhere($qb->expr()->eq('h.clubHistoryStatus', 1))
            ->having($qb->expr()->eq('max(h.clubHistoryStatus)', 1))
            ->groupBy('c.clubId')
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
            ->leftJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('m.memberId', 't.clubTeacherMember'))
            ->leftJoin(Grade::class, 'g', 'WITH', $qb->expr()->eq('m.memberId', 'g.gradeMember'))
            ->innerJoin(Club::class, 'c', 'WITH', $qb->expr()->eq('c.clubId', 't.clubTeacherClub'))
            ->where($qb->expr()->eq('t.clubTeacherTitle', 1));

        if (!is_null($club))
        {
            $qb->andWhere($qb->expr()->eq('t.clubTeacherClub', $club));
        }

        if ($mail)
        {
            $qb->andWhere($qb->expr()->isNotNull('m.memberEmail'));
        }

        return $qb->groupBy('m.memberId')
            ->orderBy('m.memberFirstname', 'ASC')
            ->addOrderBy('m.memberName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
