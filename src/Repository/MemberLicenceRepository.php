<?php
// src/Repository/MemberLicenceRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\Member;
use App\Entity\MemberLicence;

use DateTime;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MemberLicence|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemberLicence|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemberLicence[]    findAll()
 * @method MemberLicence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberLicenceRepository extends ServiceEntityRepository
{
    /**
     * MemberLicenceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemberLicence::class);
    }

    /**
     * @param Club $club
     * @return array
     */
    public function getClubActiveMemberCount(Club $club): array
    {
        $limitLow  = new DateTime('-3 month today');
        $limitHigh = new DateTime('+1 year today');

        $qb = $this->createQueryBuilder('l');

        $count['Total'] = sizeof($qb->select('count(l.memberLicenceId)')
            ->where($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.memberLicenceDeadline', "'".$limitLow->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('l.memberLicenceDeadline', "'".$limitHigh->format('Y-m-d')."'"))
            ->groupBy('l.memberLicenceMember')
            ->getQuery()
            ->getResult());

        $qb = $this->createQueryBuilder('l');

        $count['Adult'] = sizeof($qb->select('count(l.memberLicenceId)')
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->where($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.memberLicenceDeadline', "'".$limitLow->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('l.memberLicenceDeadline', "'".$limitHigh->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->neq('m.memberSubscriptionList', 2))
            ->groupBy('l.memberLicenceMember')
            ->getQuery()
            ->getResult());

        $qb = $this->createQueryBuilder('l');

        $count['Child'] = sizeof($qb->select('count(l.memberLicenceId)')
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->where($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.memberLicenceDeadline', "'".$limitLow->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('l.memberLicenceDeadline', "'".$limitHigh->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->neq('m.memberSubscriptionList', 1))
            ->groupBy('l.memberLicenceMember')
            ->getQuery()
            ->getResult());

        return $count;
    }

    /**
     * @param Club|null $club
     * @return array|null
     */
    public function getOnGoingStampLicence(?Club $club = null): ?array
    {
        $qb = $this->createQueryBuilder('l');

        $qb->select('l')
            ->where($qb->expr()->isNull('l.memberLicencePrintoutDone'))
            ->andWhere($qb->expr()->isNotNull('l.memberLicencePrintoutCreation'));

        is_null($club) ?: $qb->andWhere($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()));

        return $qb->orderBy('l.memberLicencePrintoutCreation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Club|null $club
     * @return array|null
     */
    public function getPaymentOnGoing(?Club $club = null): ?array
    {
        $qb = $this->createQueryBuilder('l');

        $qb->where($qb->expr()->isNull('l.memberLicencePaymentDate'))
            ->andWhere($qb->expr()->gt('l.memberLicenceUpdate', "'".'2021-10-01'."'" ));

        is_null($club) ?: $qb->andWhere($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()));

        return $qb->orderBy('l.memberLicenceDeadline', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array|null
     */
    public function getLastEncodedForm(): ?array
    {
        $limit = new DateTime('-90 day today');

        $qb = $this->createQueryBuilder('p');

        return $qb->select('m.memberId AS Id', 'p.memberLicenceDeadline AS Deadline', 'p.memberLicenceUpdate AS DateUpdate', 'p.memberLicencePaymentDate AS DatePayment', 'p.memberLicencePaymentUpdate AS PaymentUpdate', 'c.clubId AS ClubId', 'p.memberLicencePrintoutCreation AS StampCreation', 'p.memberLicencePrintoutDone AS StampPrint')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.memberId', 'p.memberLicenceMember'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('p.memberLicenceClub', 'c.clubId'))
            ->where($qb->expr()->gte('p.memberLicenceUpdate', "'".$limit->format('Y-m-d')."'"))
            ->orderBy('p.memberLicenceUpdate', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getLastPrintedStamp(): ?array
    {
        $limit = new DateTime('-90 day today');

        $qb = $this->createQueryBuilder('p');

        return $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'p.memberLicenceDeadline AS Deadline', 'p.memberLicencePrintoutDone AS DatePrint', 'c.clubId AS ClubId', 'c.clubName AS ClubName')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.memberId', 'p.memberLicenceMember'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('p.memberLicenceClub', 'c.clubId'))
            ->where($qb->expr()->gte('p.memberLicencePrintoutDone', "'".$limit->format('Y-m-d')."'"))
            ->orderBy('p.memberLicencePrintoutDone', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
