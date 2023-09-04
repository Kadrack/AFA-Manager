<?php
// src/Repository/MemberRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubDojo;
use App\Entity\Grade;
use App\Entity\GradeSessionCandidate;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\Training;
use App\Entity\TrainingAttendance;
use App\Entity\TrainingSession;
use App\Entity\TrainingSessionAttendance;

use DateInterval;
use DateTime;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Member|null find($id, $lockMode = null, $lockVersion = null)
 * @method Member|null findOneBy(array $criteria, array $orderBy = null)
 * @method Member[]    findAll()
 * @method Member[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemberRepository extends ServiceEntityRepository
{
    /**
     * MemberRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Member::class);
    }

    /**
     * @param Club $club
     * @param bool|null $adult
     * @param DateTime|null $date
     * @return array
     */
    public function getClubActiveMemberList(Club $club, ?bool $adult = null, ?DateTime $date = null): array
    {
        if (is_null($date))
        {
            $date = new DateTime();
        }

        $limitLow  = date_sub(clone $date, DateInterval::createFromDateString('3 month'));
        $limitHigh = date_add(clone $date, DateInterval::createFromDateString('1 year'));

        $qb = $this->createQueryBuilder('m');

        $qb->distinct()
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.member_licence_deadline', "'".$limitLow->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$limitHigh->format('Y-m-d')."'"));

        if (!is_null($adult))
        {
            if ($adult)
            {
                $qb->andWhere($qb->expr()->neq('m.member_subscription_list', 2));
            }
            else
            {
                $qb->andWhere($qb->expr()->neq('m.member_subscription_list', 1));
            }
        }

        return $qb->orderBy('m.member_firstname')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Club $club
     * @return array|null
     */
    public function getUnpayedSession(Club $club): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('m As Member')
            ->addSelect('s.grade_session_candidate_rank As Grade')
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->innerJoin(GradeSessionCandidate::class, 's', 'WITH', $qb->expr()->eq('m.member_id', 's.grade_session_candidate_member'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->isNull('s.grade_session_candidate_payment_date'))
            ->andWhere($qb->expr()->neq('s.grade_session_candidate_status', 2))
            ->orderBy('m.member_firstname', 'ASC')
            ->addOrderBy('m.member_name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Club $club
     * @return array|null
     */
    public function getOnGoingStampMember(Club $club): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->where($qb->expr()->isNull('l.member_licence_printout_done'))
            ->andWhere($qb->expr()->isNotNull('l.member_licence_printout_creation'))
            ->andWhere($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->orderBy('l.member_licence_printout_creation', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Club $club
     * @return array|null
     */
    public function getMemberToRenew(Club $club): ?array
    {
        $limitLow  = new DateTime();
        $limitHigh = date_add(clone $limitLow, DateInterval::createFromDateString('3 month'));

        $qb = $this->createQueryBuilder('m');

        return $qb->select('m As Member')
            ->addSelect('max(l.member_licence_deadline) As Deadline')
            ->distinct()
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->andWhere($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->having($qb->expr()->gte('max(l.member_licence_deadline)', "'".$limitLow->format('Y-m-d')."'"))
            ->andHaving($qb->expr()->lt('max(l.member_licence_deadline)', "'".$limitHigh->format('Y-m-d')."'"))
            ->groupBy('m.member_id')
            ->orderBy('l.member_licence_deadline')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Club $club
     * @return array|null
     */
    public function getRecentExpired(Club $club): ?array
    {
        $limitHigh = new DateTime();
        $limitLow  = date_sub(clone $limitHigh, DateInterval::createFromDateString('3 month'));

        $qb = $this->createQueryBuilder('m');

        return $qb->select('m As Member')
            ->addSelect('max(l.member_licence_deadline) As Deadline')
            ->distinct()
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->andWhere($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->having($qb->expr()->gte('max(l.member_licence_deadline)', "'".$limitLow->format('Y-m-d')."'"))
            ->andHaving($qb->expr()->lt('max(l.member_licence_deadline)', "'".$limitHigh->format('Y-m-d')."'"))
            ->groupBy('m.member_id')
            ->orderBy('l.member_licence_deadline')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $member_id
     * @return array|null
     */
    public function getMemberAttendances(int $member_id): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('t.training_id AS Id', 't.training_name AS Name', 's.training_session_date AS Date', 'sum(s.training_session_duration) AS Duration')
            ->join(TrainingAttendance::class, 'a', 'WITH', $qb->expr()->eq('m.member_id', 'a.training_attendance_member'))
            ->join(TrainingSessionAttendance::class, 'sa', 'WITH', $qb->expr()->eq('a.training_attendance_id', 'sa.training_session_attendances'))
            ->join(TrainingSession::class, 's', 'WITH', $qb->expr()->eq('s.training_session_id', 'sa.training_session'))
            ->join(Training::class, 't', 'WITH', $qb->expr()->eq('a.training', 't.training_id'))
            ->where($qb->expr()->eq('m.member_id', $member_id))
            ->andWhere($qb->expr()->eq('a.training_attendance_status', 1))
            ->groupBy('Id')
            ->orderBy('Date', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param string $search
     * @return array|null
     */
    public function getSearchMembers(string $search): ?array
    {
        $qb = $this->createQueryBuilder('m');

        $qb->select('m');

        if (ctype_digit($search))
        {
            $qb->andWhere($qb->expr()->eq('m.member_id', $search));
        }
        elseif (str_contains($search, '@'))
        {
            $qb->andWhere($qb->expr()->eq('m.member_email', "'".$search."'"));
        }
        else
        {
            $qb->andWhere($qb->expr()->like("concat(m.member_name, ' ', m.member_firstname)", "'%".$search."%'"));
        }

        return $qb->orderBy('m.member_firstname', 'ASC')
            ->addOrderBy('m.member_name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $member_id
     * @param string|null $start
     * @param string|null $end
     * @return array|null
     */
    public function getMemberAttendancesTotal(int $member_id, string $start = null, string $end = null): ?array
    {
        if (is_null($start))
        {
            $start = '1900-01-01';
        }

        if (is_null($end))
        {
            $today = new DateTime();

            $end = $today->format('Y-m-d');
        }

        $qb = $this->createQueryBuilder('m');

        return $qb->select('sum(s.training_session_duration) AS Total')
            ->join(TrainingAttendance::class, 'a', 'WITH', $qb->expr()->eq('m.member_id', 'a.training_attendance_member'))
            ->join(TrainingSessionAttendance::class, 'sa', 'WITH', $qb->expr()->eq('a.training_attendance_id', 'sa.training_session_attendances'))
            ->join(TrainingSession::class, 's', 'WITH', $qb->expr()->eq('s.training_session_id', 'sa.training_session'))
            ->join(Training::class, 't', 'WITH', $qb->expr()->eq('a.training', 't.training_id'))
            ->where($qb->expr()->eq('m.member_id', $member_id))
            ->andWhere($qb->expr()->gte('s.training_session_date', "'".$start."'"))
            ->andWhere($qb->expr()->lte('s.training_session_date', "'".$end."'"))
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param Club $club
     * @param string $start
     * @param string $end
     * @return array|null
     */
    public function getClubRenewForms(Club $club, string $start, string $end): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.member_licence_deadline', "'".$start."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$end."'"))
            ->orderBy('m.member_firstname', 'ASC')
            ->addOrderBy('m.member_name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array|null
     */
    public function getPaymentList(): ?array
    {
        $limit = new DateTime('-90 day today');

        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'l.member_licence_deadline AS Deadline', 'l.member_licence_payment_date AS Date', 'c.club_id AS ClubId', 'c.club_name AS ClubName', 'l.member_licence_id AS RenewId')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.member_licence_club', 'c.club_id'))
            ->where($qb->expr()->gte('l.member_licence_payment_date', "'".$limit->format('Y-m-d')."'"))
            ->orderBy('Date', 'DESC')
            ->addOrderBy('l.member_licence_id', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getPassSportList(): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'c.club_name AS Club', 'c.club_id AS ClubId')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.member_licence_club', 'c.club_id'))
            ->where($qb->expr()->gte('m.member_start_practice', "'".'2022-02-19'."'"))
            ->andWhere($qb->expr()->neq('c.club_id', 5000))
            ->setMaxResults(376)
            ->groupBy('Id')
            ->orderBy('ClubId', 'ASC')
            ->addOrderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getPassSportListDownload(): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.member_start_practice AS Start', 'm.member_birthday AS Birthday', 'm.member_sex AS Sex', 'c.club_name AS Club', 'd.club_dojo_zip AS Zip', 'm.member_id AS Id')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.member_licence_club', 'c.club_id'))
            ->join(ClubDojo::class, 'd', 'WITH', $qb->expr()->eq('d.club_dojo_club', 'c.club_id'))
            ->where($qb->expr()->gte('m.member_start_practice', "'".'2022-02-19'."'"))
            ->andWhere($qb->expr()->neq('c.club_id', 5000))
            ->setMaxResults(376)
            ->groupBy('Id')
            ->orderBy('Start', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getIAFList(): ?array
    {
        $today = new DateTime();

        $limitLow = new DateTime('-45 year today');
        $limitHigh = new DateTime('-25 year today');

        $qb = $this->createQueryBuilder('m');

        $list['First'] = $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'max(l.member_licence_deadline) AS Deadline', 'max(g.grade_rank) AS Grade', 'max(g.grade_date) AS GradeDate', 'm.member_phone As Phone', 'm.member_email AS Email', 'm.member_birthday AS Birthday')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.grade_member', 'm.member_id'))
            ->where($qb->expr()->between('m.member_birthday', "'".$limitLow->format('Y-m-d')."'", "'".$limitHigh->format('Y-m-d')."'"))
            ->having($qb->expr()->gt('Deadline', "'".$today->format('Y-m-d')."'"))
            ->andHaving($qb->expr()->gte('Grade', 9))
            ->andHaving($qb->expr()->lte('Grade', 14))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $limitLow = new DateTime('-25 year today');
        $limitHigh = new DateTime('-18 year today');

        $qb = $this->createQueryBuilder('m');

        $list['Second'] = $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'max(l.member_licence_deadline) AS Deadline', 'max(g.grade_rank) AS Grade', 'max(g.grade_date) AS GradeDate', 'm.member_email AS Email', 'm.member_birthday AS Birthday')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.member_licence_club', 'c.club_id'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.grade_member', 'm.member_id'))
            ->where($qb->expr()->between('m.member_birthday', "'".$limitLow->format('Y-m-d')."'", "'".$limitHigh->format('Y-m-d')."'"))
            ->having($qb->expr()->gt('Deadline', "'".$today->format('Y-m-d')."'"))
            ->andHaving($qb->expr()->gte('Grade', 7))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return $list;
    }

    /**
     * @return array|null
     */
    public function getLastExpiredList(): ?array
    {
        $limitLow = new DateTime('-6 month today');
        $limitHigh = new DateTime();

        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'c.club_name AS Club', 'c.club_id AS ClubId', 'max(l.member_licence_deadline) AS Deadline', 'm.member_start_practice AS Start', 'count(distinct c.club_id) AS Aware')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.member_licence_club', 'c.club_id'))
            ->having($qb->expr()->gte('max(l.member_licence_deadline)', "'".$limitLow->format('Y-m-d')."'"))
            ->andHaving($qb->expr()->lte('max(l.member_licence_deadline)', "'".$limitHigh->format('Y-m-d')."'"))
            ->groupBy('Id')
            ->orderBy('ClubId', 'ASC')
            ->addOrderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getKagamiList(): ?array
    {
        $today = new DateTime();

        $list['Year'] = $today->format('Y');

        $deadline = $list['Year'] . '-06-30';

        $limitShodan  = $list['Year'] - 1 . '-01-31';
        $limitNidan   = $list['Year'] - 3 . '-01-31';
        $limitSandan  = $list['Year'] - 5 . '-01-31';
        $limitYondan  = $list['Year'] - 7 . '-01-31';
        $limitGodan   = $list['Year'] - 5 . '-01-31';
        $limitRokudan = $list['Year'] - 6 . '-01-31';
        $limitNanadan = $list['Year'] - 11 . '-01-31';

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'max(l.member_licence_deadline) AS Deadline', 'max(g.grade_rank) AS Grade', 'max(g.grade_date) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.grade_member', 'm.member_id'))
            ->where($qb->expr()->eq('m.member_last_kagami', 0))
            ->having($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->andHaving($qb->expr()->eq('Grade',  18))
            ->andHaving($qb->expr()->lt('GradeDate', "'".$limitNanadan."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($result as $candidate)
        {
            $list['Candidate'][20][$candidate['Id']] = $candidate;
        }

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'max(l.member_licence_deadline) AS Deadline', 'max(g.grade_rank) AS Grade', 'max(g.grade_date) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.grade_member', 'm.member_id'))
            ->where($qb->expr()->eq('m.member_last_kagami', 0))
            ->having($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->andHaving($qb->expr()->eq('Grade', 16))
            ->andHaving($qb->expr()->lt('GradeDate', "'".$limitRokudan."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($result as $candidate)
        {
            $list['Candidate'][18][$candidate['Id']] = $candidate;
        }

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'max(l.member_licence_deadline) AS Deadline', 'max(g.grade_rank) AS Grade', 'max(g.grade_date) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.grade_member', 'm.member_id'))
            ->where($qb->expr()->eq('m.member_last_kagami', 0))
            ->having($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->andHaving($qb->expr()->eq('Grade', 14))
            ->andHaving($qb->expr()->lt('GradeDate', "'".$limitGodan."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($result as $candidate)
        {
            $list['Candidate'][16][$candidate['Id']] = $candidate;
        }

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'max(l.member_licence_deadline) AS Deadline', 'max(g.grade_rank) AS Grade', 'max(g.grade_date) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.grade_member', 'm.member_id'))
            ->where($qb->expr()->eq('m.member_last_kagami', 0))
            ->having($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->andHaving($qb->expr()->eq('Grade', 12))
            ->andHaving($qb->expr()->lt('GradeDate', "'".$limitYondan."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($result as $candidate)
        {
            $list['Candidate'][14][$candidate['Id']] = $candidate;
        }

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'max(l.member_licence_deadline) AS Deadline', 'max(g.grade_rank) AS Grade', 'max(g.grade_date) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.grade_member', 'm.member_id'))
            ->where($qb->expr()->eq('m.member_last_kagami', 0))
            ->having($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->andHaving($qb->expr()->eq('Grade', 10))
            ->andHaving($qb->expr()->lt('GradeDate', "'".$limitSandan."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($result as $candidate)
        {
            $list['Candidate'][12][$candidate['Id']] = $candidate;
        }

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'max(l.member_licence_deadline) AS Deadline', 'max(g.grade_rank) AS Grade', 'max(g.grade_date) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.grade_member', 'm.member_id'))
            ->where($qb->expr()->eq('m.member_last_kagami', 0))
            ->having($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->andHaving($qb->expr()->eq('Grade', 8))
            ->andHaving($qb->expr()->lt('GradeDate', "'".$limitNidan."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($result as $candidate)
        {
            $list['Candidate'][10][$candidate['Id']] = $candidate;
        }

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.member_id AS Id', 'm.member_firstname AS FirstName', 'm.member_name AS Name', 'max(l.member_licence_deadline) AS Deadline', 'max(g.grade_rank) AS Grade', 'max(g.grade_date) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.member_id', 'l.member_licence'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.grade_member', 'm.member_id'))
            ->where($qb->expr()->eq('m.member_last_kagami', 0))
            ->having($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->andHaving($qb->expr()->eq('max(g.grade_rank)', 6))
            ->andHaving($qb->expr()->lt('max(g.grade_date)', "'".$limitShodan."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($result as $candidate)
        {
            $list['Candidate'][8][$candidate['Id']] = $candidate;
        }

        return $list;
    }
}
