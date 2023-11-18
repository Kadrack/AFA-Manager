<?php
// src/Repository/ClubRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubDojo;
use App\Entity\ClubHistory;
use App\Entity\ClubTeacher;
use App\Entity\Formation;
use App\Entity\Grade;
use App\Entity\Member;
use App\Entity\MemberLicence;

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
     * @param bool|null $active
     * @param Club|null $club
     *
     * @return array|null
     */
    public function getClubList(?bool $active = true, ?Club $club = null): ?array
    {
        $qb = $this->createQueryBuilder('c');

        $qb->innerJoin(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.clubHistoryClub', 'c.clubId'));

        if (!is_null($club))
        {
            $qb->where($qb->expr()->eq('c.clubId', $club->getClubId()));
        }
        else
        {
            if (is_null($active))
            {
                $qb->having($qb->expr()->eq('max(h.clubHistoryStatus)', 1));
            }
            elseif ($active)
            {
                $qb->having($qb->expr()->lte('max(h.clubHistoryStatus)', 2));
            }
            else
            {
                $qb->having($qb->expr()->eq('max(h.clubHistoryStatus)', 3));
            }
        }

        return $qb->groupBy('c.clubId')
            ->orderBy('c.clubId', 'ASC')
            ->getQuery()
            ->getResult();
    }







    /**
     * @param DateTime $referenceDate
     * @param int|null $province
     * @return array|null
     */
    public function getMembers(DateTime $referenceDate, ?int $province = null): ?array
    {
        $deadlineLow = date('Y-m-d', $referenceDate->getTimestamp());

        $deadlineHigh = date('Y-m-d', strtotime('+1 year', $referenceDate->getTimestamp()));

        $qb = $this->createQueryBuilder('c');

        $qb->select('m.memberId AS Id', 'c.clubProvince AS Province', 'c.clubId AS ClubId', 'c.clubName AS ClubName', 'm.memberSex AS Sex', 'm.memberBirthday AS Birthday', 'max(g.gradeRank) AS Grade', 'max(f.formationRank) AS Level')
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('c.clubId', 'l.memberLicenceClub'))
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('l.memberLicenceMember', 'm.memberId'))
            ->leftJoin(Grade::class, 'g', 'WITH', $qb->expr()->eq('m.memberId', 'g.gradeMember'))
            ->leftJoin(Formation::class, 'f', 'WITH', $qb->expr()->eq('m.memberId', 'f.formationMember'))
            ->where($qb->expr()->lt('g.gradeStatus', 4))
            ->andWhere($qb->expr()->orX($qb->expr()->lte('g.gradeDate', "'".$deadlineLow."'"), $qb->expr()->isNull('g.gradeDate')))
            ->andWhere($qb->expr()->orX($qb->expr()->lte('f.formationDate', "'".$deadlineLow."'"), $qb->expr()->isNull('f.formationDate')))
            ->andWhere($qb->expr()->andX($qb->expr()->gt('l.memberLicenceDeadline', "'".$deadlineLow."'"), $qb->expr()->lte('l.memberLicenceDeadline', "'".$deadlineHigh."'")));

        if (!is_null($province))
        {
            $qb->andWhere($qb->expr()->eq('c.clubProvince', $province));
        }

        return $qb->groupBy('Id')
            ->orderBy('ClubId')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getCreationDateList(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.clubId AS Id', 'c.clubName AS Name', 'c.clubCreation AS Creation', 'h.clubHistoryUpdate AS Affiliation')
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.clubHistoryClub', 'c.clubId'))
            ->having($qb->expr()->eq('max(h.clubHistoryStatus)', 1))
            ->groupBy('c.clubId')
            ->orderBy('c.clubName', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getClubsDetailsFWB(): ?array
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->select('c.clubId AS Id', 'c.clubName AS Name', 'c.clubAddress AS Address', 'c.clubZip AS Zip', 'c.clubCity AS City', 'd.clubDojoId AS DojoId', 'd.clubDojoStreet AS AddressDojo', 'd.clubDojoZip AS ZipDojo', 'd.clubDojoCity AS CityDojo', 'c.clubPresident AS President', 'c.clubSecretary AS Secretary', 'c.clubTreasurer AS Treasurer', 't.clubTeacherId AS TeacherId', 'm.memberFirstname AS TeacherFirstname', 'm.memberName AS TeacherName', 'f.formationId AS Formation', 'c.clubEmailPublic AS Email', 'd.clubDojoDea AS DEA', 'h.clubHistoryStatus AS Status')
            ->join(ClubDojo::class, 'd', 'WITH', $qb->expr()->eq('d.clubDojoClub', 'c.clubId'))
            ->join(ClubHistory::class, 'h', 'WITH', $qb->expr()->eq('h.clubHistoryClub', 'c.clubId'))
            ->join(ClubTeacher::class, 't', 'WITH', $qb->expr()->eq('t.clubTeacherClub', 'c.clubId'))
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.memberId', 't.clubTeacherMember'))
            ->leftJoin(Formation::class, 'f', 'WITH', $qb->expr()->eq('m.memberId', 'f.formationMember'))
            ->addOrderBy('c.clubId', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
