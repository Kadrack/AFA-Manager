<?php
// src/Repository/TrainingAttendanceRepositorytory.php
namespace App\Repository;

use App\Entity\Member;
use App\Entity\Training;
use App\Entity\TrainingAttendance;

use App\Entity\TrainingSessionAttendance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainingAttendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingAttendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingAttendance[]    findAll()
 * @method TrainingAttendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingAttendanceRepository extends ServiceEntityRepository
{
    /**
     * TrainingAttendanceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrainingAttendance::class);
    }

    /**
     * @param string $search
     * @param int $training
     * @return array|null
     */
    public function getFullSearchMembers(string $search, int $training): ?array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->select('a.trainingAttendanceId AS Id', 'a.trainingAttendanceName AS Name')
            ->where($qb->expr()->eq('a.trainingAttendanceTraining', $training))
            ->andWhere($qb->expr()->like('a.trainingAttendanceName', "'%".$search."%'"))
            ->orderBy('Name', 'ASC')
            ->addOrderBy('Id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param Training $training
     *
     * @return array|null
     */
    public function getTrainingAttendances(Training $training): ?array
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->select('a.trainingAttendanceId as AttendanceId', 'm.memberId as Id', 'm.memberFirstname as Firstname', 'm.memberName as Name', 'a.trainingAttendanceName as FullName', 'a.trainingAttendancePaymentCash as Cash', 'a.trainingAttendancePaymentCard as Card', 'a.trainingAttendancePaymentTransfert as Transfert', 'a.trainingAttendancePaymentDiscount as Discount', 'a.trainingAttendanceStatus as Status', 'count(sa.trainingSessionAttendanceTrainingAttendance) as Attendance')
            ->leftJoin(Member::class, 'm', 'WITH', $qb->expr()->eq('m.memberId', 'a.trainingAttendanceMember'))
            ->leftJoin(TrainingSessionAttendance::class, 'sa', 'WITH', $qb->expr()->eq('sa.trainingSessionAttendanceTrainingAttendance', 'a.trainingAttendanceId'))
            ->where($qb->expr()->eq('a.trainingAttendanceTraining', $training->getTrainingId()))
            ->groupBy('AttendanceId')
            ->orderBy('a.trainingAttendanceId', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
}
