<?php
// src/Repository/TrainingSessionAttendanceRepository.php
namespace App\Repository;

use App\Entity\Club;
use App\Entity\Member;
use App\Entity\MemberLicence;
use App\Entity\TrainingAttendance;
use App\Entity\TrainingSession;
use App\Entity\TrainingSessionAttendance;

use DateTime;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainingSessionAttendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingSessionAttendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingSessionAttendance[]    findAll()
 * @method TrainingSessionAttendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */

class TrainingSessionAttendanceRepository extends ServiceEntityRepository
{
    /**
     * TrainingSessionAttendanceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainingSessionAttendance::class);
    }

    /**
     * @param Club $club
     * @return array|null
     */
    public function getClubAttendanceTotalHour(Club $club): ?array
    {
        $lowLimit  = new DateTime();
        $highLimit = new DateTime('+1 year today');

        $qb = $this->createQueryBuilder('sa');

        return $qb->select('m.member_id AS Id', 'sum(s.training_session_duration) AS Total')
            ->innerJoin(TrainingSession::class, 's', 'WITH', $qb->expr()->eq('s.training_session_id', 'sa.training_session'))
            ->innerJoin(TrainingAttendance::class, 'a', 'WITH', $qb->expr()->eq('a.training_attendance_id', 'sa.training_session_attendances'))
            ->innerJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('a.training_attendance_member', 'm.member_id'))
            ->innerJoin(MemberLicence::class, 'l', 'WITH', $qb->expr()->eq('l.member_licence', 'm.member_id'))
            ->where($qb->expr()->eq('l.member_licence_club', $club->getClubId()))
            ->andWhere($qb->expr()->eq('a.training_attendance_status', 1))
            ->andWhere($qb->expr()->gt('l.member_licence_deadline', "'".$lowLimit->format('Y-m-d')."'"))
            ->andWhere($qb->expr()->lte('l.member_licence_deadline', "'".$highLimit->format('Y-m-d')."'"))
            ->groupBy('m.member_id')
            ->getQuery()
            ->getArrayResult();
    }
}
