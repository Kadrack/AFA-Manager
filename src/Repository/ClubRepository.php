<?php
// src/Repository/ClubRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubDojo;
use App\Entity\ClubHistory;
use App\Entity\ClubManager;
use App\Entity\ClubTeacher;
use App\Entity\Formation;
use App\Entity\Grade;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\User;

use DateTime;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Club|null find($id, $lockMode = null, $lockVersion = null)
 * @method Club|null findOneBy(array $criteria, array $orderBy = null)
 * @method Club[]    findAll()
 * @method Club[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubRepository extends ServiceEntityRepository
{
    /**
     * ClubRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Club::class);
    }

    /**
     * @param Member|null $member
     * @param User|null $user
     * @param bool|null $active
     * @return array|null
     */
    public function getClubList(?Member $member, ?User $user, ?bool $active): ?array
    {
        $qb = $this->createQueryBuilder('c');

        $qb->innerJoin(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'c.club_id'))
            ->leftJoin(ClubManager::class, 'm', 'WITH', $qb->expr()->eq('m.club_manager_club', 'c.club_id'))
            ->leftJoin(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher', 'c.club_id'));

        if (!is_null($member))
        {
            $qb->where($qb->expr()->eq('m.club_manager_member', $member->getMemberId()))
                ->orWhere($qb->expr()->eq('t.club_teacher_member', $member->getMemberId()));
        }
        elseif (!is_null($user))
        {
            $qb->where($qb->expr()->eq('m.club_manager_user', $user->getId()));
        }

        if ($active)
        {
            $qb->having($qb->expr()->lte('max(h.club_history_status)', 2));
        }
        else
        {
            $qb->having($qb->expr()->eq('max(h.club_history_status)', 3));
        }

        return $qb->groupBy('c.club_id')
            ->orderBy('c.club_id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param DateTime|null $referenceDate
     * @param int|null $province
     * @param int|null $club
     * @param bool|null $inactive
     * @return array|null
     */
    public function getMembers(?DateTime $referenceDate, ?int $province, ?int $club, ?bool $inactive = null): ?array
    {
        $today = new DateTime();

        if (is_null($inactive))
        {
            $deadlineLow = date('Y-m-d', $referenceDate->getTimestamp());

            $deadlineHigh = date('Y-m-d', strtotime('+1 year', $today->getTimestamp()));
        }
        elseif ($inactive)
        {
            $deadlineLow = date('Y-m-d', strtotime('-3 month', $today->getTimestamp()));

            $deadlineHigh = date('Y-m-d', $today->getTimestamp());
        }
        else
        {
            $deadlineLow = date('Y-m-d', $today->getTimestamp());

            $deadlineHigh = date('Y-m-d', strtotime('+3 month', $today->getTimestamp()));
        }

        $qb = $this->createQueryBuilder('c');

        $qb->select('m.member_id AS Id', 'm.member_firstname AS Firstname', 'm.member_name AS Name', 'm.member_email AS Mail', 'c.club_province AS Province', 'c.club_id AS ClubId', 'c.club_name AS ClubName', 'm.member_sex AS Sex', 'm.member_birthday AS Birthday', 'max(g.grade_rank) AS Grade', 'max(f.formation_rank) AS Level', 'max(l.member_licence_deadline) AS Deadline')
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('c.club_id', 'l.member_licence_club'))
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('l.member_licence', 'm.member_id'))
            ->innerJoin(Grade::class, 'g', 'WITH', $qb->expr()->eq('m.member_id', 'g.grade_member'))
            ->leftJoin(Formation::class, 'f', 'WITH', $qb->expr()->eq('m.member_id', 'f.formation_member'));

        if (!is_null($province))
        {
            $qb->andWhere($qb->expr()->eq('c.club_province', $province));
        }
        elseif (!is_null($club))
        {
            $qb->andWhere($qb->expr()->eq('c.club_id', $club));
        }

        $qb->andWhere($qb->expr()->lt('g.grade_status', 4));

        if (is_null($inactive))
        {
            $qb->andWhere($qb->expr()->gt('l.member_licence_deadline', "'".$deadlineLow."'"))
                ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$deadlineHigh."'"));
        }

        $qb->having($qb->expr()->gt('max(l.member_licence_deadline)', "'".$deadlineLow."'"))
            ->andHaving($qb->expr()->lte('max(l.member_licence_deadline)', "'".$deadlineHigh."'"));

        return $qb->groupBy('Id')
            ->orderBy('ClubId')
            ->addOrderBy('Firstname')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getCreationDateList(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_id AS Id', 'c.club_name AS Name', 'c.club_creation AS Creation', 'h.club_history_update AS Affiliation')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'c.club_id'))
            ->having($qb->expr()->eq('max(h.club_history_status)', 1))
            ->groupBy('c.club_id')
            ->orderBy('c.club_name', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getClubsDetailsFWB(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.club_id AS Id', 'c.club_name AS Name', 'c.club_address AS Address', 'c.club_zip AS Zip', 'c.club_city AS City', 'd.club_dojo_id AS DojoId', 'd.club_dojo_street AS AddressDojo', 'd.club_dojo_zip AS ZipDojo', 'd.club_dojo_city AS CityDojo', 'c.club_president AS President', 'c.club_secretary AS Secretary', 'c.club_treasurer AS Treasurer', 't.club_teacher_id AS TeacherId', 'm.member_firstname AS TeacherFirstname', 'm.member_name AS TeacherName', 'f.formation_id AS Formation', 'c.club_email_public AS Email', 'd.club_dojo_dea AS DEA', 'h.club_history_status AS Status')
            ->join(ClubDojo::class, 'd', 'WITH', $qb->expr()->eq('d.club_dojo_club', 'c.club_id'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.club_history', 'c.club_id'))
            ->join(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.club_teacher', 'c.club_id'))
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 't.club_teacher_member'))
            ->leftJoin(Formation::class, 'f', 'WITH', $qb->expr()->eq('m.member_id', 'f.formation_member'))
            ->addOrderBy('c.club_id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
