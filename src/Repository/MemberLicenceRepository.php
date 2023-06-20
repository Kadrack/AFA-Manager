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

        $count['Total'] = sizeof($qb->select('count(l.member_licence_id)')
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.member_licence_deadline', "'".$limitLow->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('l.member_licence_deadline', "'".$limitHigh->format('Y-m-d')."'"))
            ->groupBy('l.member_licence')
            ->getQuery()
            ->getResult());

        $qb = $this->createQueryBuilder('l');

        $count['Adult'] = sizeof($qb->select('count(l.member_licence_id)')
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.member_licence_deadline', "'".$limitLow->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('l.member_licence_deadline', "'".$limitHigh->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->neq('m.member_subscription_list', 2))
            ->groupBy('l.member_licence')
            ->getQuery()
            ->getResult());

        $qb = $this->createQueryBuilder('l');

        $count['Child'] = sizeof($qb->select('count(l.member_licence_id)')
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.member_licence_deadline', "'".$limitLow->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('l.member_licence_deadline', "'".$limitHigh->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->neq('m.member_subscription_list', 1))
            ->groupBy('l.member_licence')
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
            ->where($qb->expr()->isNull('l.member_licence_printout_done'))
            ->andWhere($qb->expr()->isNotNull('l.member_licence_printout_creation'));

        is_null($club) ?: $qb->andWhere($qb->expr()->eq('l.member_licence_club', $club->getClubId()));

        return $qb->orderBy('l.member_licence_printout_creation', 'ASC')
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

        $qb->where($qb->expr()->isNull('l.member_licence_payment_date'))
            ->andWhere($qb->expr()->gt('l.member_licence_update', "'".'2021-10-01'."'" ));

        is_null($club) ?: $qb->andWhere($qb->expr()->eq('l.member_licence_club', $club->getClubId()));

        return $qb->orderBy('l.member_licence_deadline', 'ASC')
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

        return $qb->select('m.member_id AS Id', 'p.member_licence_deadline AS Deadline', 'p.member_licence_update AS DateUpdate', 'p.member_licence_payment_date AS DatePayment', 'p.member_licence_payment_update AS PaymentUpdate', 'c.club_id AS ClubId', 'p.member_licence_printout_creation AS StampCreation', 'p.member_licence_printout_done AS StampPrint')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 'p.member_licence'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('p.member_licence_club', 'c.club_id'))
            ->where($qb->expr()->gte('p.member_licence_update', "'".$limit->format('Y-m-d')."'"))
            ->orderBy('p.member_licence_update', 'DESC')
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

        return $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'p.member_licence_deadline AS Deadline', 'p.member_licence_printout_done AS DatePrint', 'c.club_id AS ClubId', 'c.club_name AS ClubName')
            ->join(Member::class, 'm', 'WITH', $qb->expr()->eq('m.member_id', 'p.member_licence'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('p.member_licence_club', 'c.club_id'))
            ->where($qb->expr()->gte('p.member_licence_printout_done', "'".$limit->format('Y-m-d')."'"))
            ->orderBy('p.member_licence_printout_done', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
