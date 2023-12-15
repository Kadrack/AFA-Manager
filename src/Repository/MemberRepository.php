<?php
// src/Repository/MemberRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\ClubDojo;
use App\Entity\Formation;
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
     * @param Club     $club
     * @param int|null $year
     *
     * @return array
     */
    public function getClubMemberListTest(Club $club, ?int $year = null): array
    {
        if (is_null($year))
        {
            $today = new DateTime();

            if (intval($today->format('m')) >= 8)
            {
                $year = intval((new DateTime())->format('Y'));
            }
            else
            {
                $year = intval((new DateTime())->format('Y')) - 1;
            }
        }

        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.memberId as Id', 'm.memberFirstname as Firstname', 'm.memberName as Name', 'm.memberSubscriptionList as List', 'm.memberSubscriptionStatus as Status', 'm.memberSubscriptionValidity as Validity', 'max(l.memberLicenceDeadline) as Deadline', 'max(l.memberLicencePaymentDate) as Payment', 'max(l.memberLicencePrintoutDone) as Stamp')
            ->distinct()
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->where($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.memberLicenceDeadline', "'" . $year   . "-08-01" . "'"))
            ->andWhere($qb->expr()->lte('l.memberLicenceDeadline', "'" . $year+2 . "-07-31" . "'"))
            ->groupBy('Id')
            ->orderBy('Firstname')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param Club          $club
     * @param bool|null     $adult
     * @param DateTime|null $date
     *
     * @return array
     */
    public function getClubMemberList(Club $club, ?bool $adult = null, ?DateTime $date = null): array
    {
        if (is_null($date))
        {
            $date = new DateTime();
        }

        $limitLow  = date_sub(clone $date, DateInterval::createFromDateString('3 month'));
        $limitHigh = date_add(clone $date, DateInterval::createFromDateString('1 year'));

        $qb = $this->createQueryBuilder('m');

        $qb->select('m.memberId as Id', 'm.memberFirstname as Firstname', 'm.memberName as Name')
            ->distinct()
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->where($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->andWhere($qb->expr()->gt('l.memberLicenceDeadline', "'".$limitLow->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.memberLicenceDeadline', "'".$limitHigh->format('Y-m-d')."'"));

        if (!is_null($adult))
        {
            if ($adult)
            {
                $qb->andWhere($qb->expr()->neq('m.memberSubscriptionList', 2));
            }
            else
            {
                $qb->andWhere($qb->expr()->neq('m.memberSubscriptionList', 1));
            }
        }

        return $qb->orderBy('m.memberFirstname')
            ->getQuery()
            ->getArrayResult();
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
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->where($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.memberLicenceDeadline', "'".$limitLow->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.memberLicenceDeadline', "'".$limitHigh->format('Y-m-d')."'"));

        if (!is_null($adult))
        {
            if ($adult)
            {
                $qb->andWhere($qb->expr()->neq('m.memberSubscriptionList', 2));
            }
            else
            {
                $qb->andWhere($qb->expr()->neq('m.memberSubscriptionList', 1));
            }
        }

        return $qb->orderBy('m.memberFirstname')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array
     */
    public function getActiveMemberEmailList(): array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.memberEmail As Email')
            ->distinct()
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->where($qb->expr()->gte('l.memberLicenceDeadline', "'".(new DateTime())->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->isNotNull('m.memberEmail'))
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
            ->addSelect('s.gradeSessionCandidateRank As Grade')
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->innerJoin(GradeSessionCandidate::class, 's', 'WITH', $qb->expr()->eq('m.memberId', 's.gradeSessionCandidateMember'))
            ->where($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->andWhere($qb->expr()->isNull('s.gradeSessionCandidatePaymentDate'))
            ->andWhere($qb->expr()->neq('s.gradeSessionCandidateStatus', 2))
            ->orderBy('m.memberFirstname', 'ASC')
            ->addOrderBy('m.memberName', 'ASC')
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

        return $qb->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->where($qb->expr()->isNull('l.memberLicencePrintoutDone'))
            ->andWhere($qb->expr()->isNotNull('l.memberLicencePrintoutCreation'))
            ->andWhere($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->orderBy('l.memberLicencePrintoutCreation', 'ASC')
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

        return $qb->distinct()
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->andWhere($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->having($qb->expr()->gte('max(l.memberLicenceDeadline)', "'".$limitLow->format('Y-m-d')."'"))
            ->andHaving($qb->expr()->lt('max(l.memberLicenceDeadline)', "'".$limitHigh->format('Y-m-d')."'"))
            ->groupBy('m.memberId')
            ->orderBy('l.memberLicenceDeadline')
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

        return $qb->distinct()
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->andWhere($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->having($qb->expr()->gte('max(l.memberLicenceDeadline)', "'".$limitLow->format('Y-m-d')."'"))
            ->andHaving($qb->expr()->lt('max(l.memberLicenceDeadline)', "'".$limitHigh->format('Y-m-d')."'"))
            ->groupBy('m.memberId')
            ->orderBy('l.memberLicenceDeadline')
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

        return $qb->select('t.trainingId AS Id', 't.trainingName AS Name', 's.trainingSessionDate AS Date', 'sum(s.trainingSessionDuration) AS Duration')
            ->join(TrainingAttendance::class, 'a', 'WITH', $qb->expr()->eq('m.memberId', 'a.trainingAttendanceMember'))
            ->join(TrainingSessionAttendance::class, 'sa', 'WITH', $qb->expr()->eq('a.trainingAttendanceId', 'sa.trainingSessionAttendanceTrainingAttendance'))
            ->join(TrainingSession::class, 's', 'WITH', $qb->expr()->eq('s.trainingSessionId', 'sa.trainingSessionAttendanceTrainingSession'))
            ->join(Training::class, 't', 'WITH', $qb->expr()->eq('a.trainingAttendanceTraining', 't.trainingId'))
            ->where($qb->expr()->eq('m.memberId', $member_id))
            ->andWhere($qb->expr()->eq('a.trainingAttendanceStatus', 1))
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
            $qb->andWhere($qb->expr()->eq('m.memberId', $search));
        }
        elseif (str_contains($search, '@'))
        {
            $qb->andWhere($qb->expr()->eq('m.memberEmail', "'".$search."'"));
        }
        else
        {
            $qb->andWhere($qb->expr()->like("concat(m.memberName, ' ', m.memberFirstname)", "'%".$search."%'"));
            $qb->orWhere($qb->expr()->like("concat(m.memberFirstname, ' ', m.memberName)", "'%".$search."%'"));
        }

        return $qb->orderBy('m.memberFirstname', 'ASC')
            ->addOrderBy('m.memberName', 'ASC')
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

        return $qb->select('sum(s.trainingSessionDuration) AS Total')
            ->join(TrainingAttendance::class, 'a', 'WITH', $qb->expr()->eq('m.memberId', 'a.trainingAttendanceMember'))
            ->join(TrainingSessionAttendance::class, 'sa', 'WITH', $qb->expr()->eq('a.trainingAttendanceId', 'sa.trainingSessionAttendanceTrainingAttendance'))
            ->join(TrainingSession::class, 's', 'WITH', $qb->expr()->eq('s.trainingSessionId', 'sa.trainingSessionAttendanceTrainingSession'))
            ->join(Training::class, 't', 'WITH', $qb->expr()->eq('a.trainingAttendanceTraining', 't.trainingId'))
            ->where($qb->expr()->eq('m.memberId', $member_id))
            ->andWhere($qb->expr()->gte('s.trainingSessionDate', "'".$start."'"))
            ->andWhere($qb->expr()->lte('s.trainingSessionDate', "'".$end."'"))
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

        return $qb->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->where($qb->expr()->eq('l.memberLicenceClub', $club->getClubId()))
            ->andWhere($qb->expr()->gte('l.memberLicenceDeadline', "'".$start."'"))
            ->andWhere($qb->expr()->lt('l.memberLicenceDeadline', "'".$end."'"))
            ->orderBy('m.memberFirstname', 'ASC')
            ->addOrderBy('m.memberName', 'ASC')
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

        return $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'l.memberLicenceDeadline AS Deadline', 'l.memberLicencePaymentDate AS Date', 'c.clubId AS ClubId', 'c.clubName AS ClubName', 'l.memberLicenceId AS RenewId')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.memberLicenceClub', 'c.clubId'))
            ->where($qb->expr()->gte('l.memberLicencePaymentDate', "'".$limit->format('Y-m-d')."'"))
            ->orderBy('Date', 'DESC')
            ->addOrderBy('l.memberLicenceId', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return array|null
     */
    public function getPassSportList(): ?array
    {
        $qb = $this->createQueryBuilder('m');

        return $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'c.clubName AS Club', 'c.clubId AS ClubId')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.memberLicenceClub', 'c.clubId'))
            ->where($qb->expr()->gte('m.memberStartPractice', "'".'2022-02-19'."'"))
            ->andWhere($qb->expr()->neq('c.clubId', 5000))
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

        return $qb->select('m.memberStartPractice AS Start', 'm.memberBirthday AS Birthday', 'm.memberSex AS Sex', 'c.clubName AS Club', 'd.clubDojoZip AS Zip', 'm.memberId AS Id')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.memberLicenceClub', 'c.clubId'))
            ->join(ClubDojo::class, 'd', 'WITH', $qb->expr()->eq('d.clubDojoClub', 'c.clubId'))
            ->where($qb->expr()->gte('m.memberStartPractice', "'".'2022-02-19'."'"))
            ->andWhere($qb->expr()->neq('c.clubId', 5000))
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

        $list['First'] = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade', 'max(g.gradeDate) AS GradeDate', 'm.memberPhone As Phone', 'm.memberEmail AS Email', 'm.memberBirthday AS Birthday')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->between('m.memberBirthday', "'".$limitLow->format('Y-m-d')."'", "'".$limitHigh->format('Y-m-d')."'"))
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

        $list['Second'] = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade', 'max(g.gradeDate) AS GradeDate', 'm.memberEmail AS Email', 'm.memberBirthday AS Birthday')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.memberLicenceClub', 'c.clubId'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->between('m.memberBirthday', "'".$limitLow->format('Y-m-d')."'", "'".$limitHigh->format('Y-m-d')."'"))
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

        return $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'c.clubName AS Club', 'c.clubId AS ClubId', 'max(l.memberLicenceDeadline) AS Deadline', 'm.memberStartPractice AS Start', 'count(distinct c.clubId) AS Aware')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Club::class, 'c', 'WITH', $qb->expr()->eq('l.memberLicenceClub', 'c.clubId'))
            ->having($qb->expr()->gte('max(l.memberLicenceDeadline)', "'".$limitLow->format('Y-m-d')."'"))
            ->andHaving($qb->expr()->lte('max(l.memberLicenceDeadline)', "'".$limitHigh->format('Y-m-d')."'"))
            ->groupBy('Id')
            ->orderBy('Start', 'ASC')
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
        $limitGodan   = $list['Year'] - 4 . '-01-31';
        $limitRokudan = $list['Year'] - 5 . '-01-31';
        $limitNanadan = $list['Year'] - 11 . '-01-31';

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade', 'max(g.gradeDate) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->eq('m.memberLastKagami', 0))
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

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade', 'max(g.gradeDate) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->eq('m.memberLastKagami', 0))
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

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade', 'max(g.gradeDate) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->eq('m.memberLastKagami', 0))
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

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade', 'max(g.gradeDate) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->eq('m.memberLastKagami', 0))
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

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade', 'max(g.gradeDate) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->eq('m.memberLastKagami', 0))
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

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade', 'max(g.gradeDate) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->eq('m.memberLastKagami', 0))
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

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade', 'max(g.gradeDate) AS GradeDate')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->eq('m.memberLastKagami', 0))
            ->having($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->andHaving($qb->expr()->eq('max(g.gradeRank)', 6))
            ->andHaving($qb->expr()->lt('max(g.gradeDate)', "'".$limitShodan."'"))
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

    /**
     * @return array
     */
    public function getCPAnimateurCandidate(): array
    {
        $limitLicence = new DateTime('-3 month today');

        $limitBirthday = new DateTime('-17 year today');

        $qb = $this->createQueryBuilder('m');

        return $qb->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicence'))
            ->innerJoin(Grade::class, 'g', 'WITH', $qb->expr()->eq('m.memberId', 'g.gradeMember'))
            ->leftJoin(Formation::class, 'f', 'WITH', $qb->expr()->eq('m.memberId', 'f.formationMember'))
            ->where($qb->expr()->gte('l.memberLicenceDeadline', "'".$limitLicence->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('m.memberBirthday', "'".$limitBirthday->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->isNotNull('m.memberEmail'))
            ->groupBy('m.memberId')
            ->having($qb->expr()->lt('max(f.formationRank)', 4))
            ->orHaving($qb->expr()->isNull('max(f.formationRank)'))
            ->andHaving($qb->expr()->gte('max(g.gradeRank)', 5))
            ->getQuery()
            ->getResult();
    }

    public function getStatGrade(): array
    {
        $lowLimit = new DateTime();

        $highLimit = new DateTime('+1 year today');

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->gt('l.memberLicenceDeadline', "'".$lowLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.memberLicenceDeadline', "'".$highLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('g.gradeStatus', 4))
            ->having($qb->expr()->eq('max(g.gradeRank)', 7))
            ->orHaving($qb->expr()->eq('max(g.gradeRank)', 8))
            //->andHaving($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $stat['1er Dan'] = sizeof($result);

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->gt('l.memberLicenceDeadline', "'".$lowLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.memberLicenceDeadline', "'".$highLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('g.gradeStatus', 4))
            ->having($qb->expr()->eq('max(g.gradeRank)', 9))
            ->orHaving($qb->expr()->eq('max(g.gradeRank)', 10))
            //->andHaving($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $stat['2ème Dan'] = sizeof($result);

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->gt('l.memberLicenceDeadline', "'".$lowLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.memberLicenceDeadline', "'".$highLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('g.gradeStatus', 4))
            ->having($qb->expr()->eq('max(g.gradeRank)', 11))
            ->orHaving($qb->expr()->eq('max(g.gradeRank)', 12))
            //->andHaving($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $stat['3ème Dan'] = sizeof($result);

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->gt('l.memberLicenceDeadline', "'".$lowLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.memberLicenceDeadline', "'".$highLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('g.gradeStatus', 4))
            ->having($qb->expr()->eq('max(g.gradeRank)', 13))
            ->orHaving($qb->expr()->eq('max(g.gradeRank)', 14))
            //->andHaving($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $stat['4ème Dan'] = sizeof($result);

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->gt('l.memberLicenceDeadline', "'".$lowLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.memberLicenceDeadline', "'".$highLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('g.gradeStatus', 4))
            ->having($qb->expr()->eq('max(g.gradeRank)', 15))
            ->orHaving($qb->expr()->eq('max(g.gradeRank)', 16))
            //->andHaving($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $stat['5ème Dan'] = sizeof($result);

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->gt('l.memberLicenceDeadline', "'".$lowLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.memberLicenceDeadline', "'".$highLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('g.gradeStatus', 4))
            ->having($qb->expr()->eq('max(g.gradeRank)', 17))
            ->orHaving($qb->expr()->eq('max(g.gradeRank)', 18))
            //->andHaving($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $stat['6ème Dan'] = sizeof($result);

        $qb = $this->createQueryBuilder('m');

        $result = $qb->select('m.memberId AS Id', 'm.memberFirstname AS FirstName', 'm.memberName AS Name', 'max(l.memberLicenceDeadline) AS Deadline', 'max(g.gradeRank) AS Grade')
            ->join(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('m.memberId', 'l.memberLicenceMember'))
            ->join(Grade::class, 'g', 'WITH', $qb->expr()->eq('g.gradeMember', 'm.memberId'))
            ->where($qb->expr()->gt('l.memberLicenceDeadline', "'".$lowLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.memberLicenceDeadline', "'".$highLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lt('g.gradeStatus', 4))
            ->having($qb->expr()->eq('max(g.gradeRank)', 19))
            ->orHaving($qb->expr()->eq('max(g.gradeRank)', 20))
            //->andHaving($qb->expr()->gt('Deadline', "'".$deadline."'"))
            ->groupBy('Id')
            ->orderBy('FirstName', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $stat['7ème Dan'] = sizeof($result);

        return $stat;
    }
}
