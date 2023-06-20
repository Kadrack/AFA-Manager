<?php
// src/Repository/TrainingAttendanceRepositorytory.php
namespace App\Repository;

use App\Entity\Training;
use App\Entity\TrainingAttendance;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\ORM\Tools\Pagination\Paginator;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TrainingAttendance|null find($id, $lockMode = null, $lockVersion = null)
 * @method TrainingAttendance|null findOneBy(array $criteria, array $orderBy = null)
 * @method TrainingAttendance[]    findAll()
 * @method TrainingAttendance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingAttendanceRepository extends ServiceEntityRepository
{
    public int $attendancesPerPage = 15;

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

        return $qb->select('a.training_attendance_id AS Id', 'a.training_attendance_name AS Name')
            ->where($qb->expr()->eq('a.training', $training))
            ->andWhere($qb->expr()->like('a.training_attendance_name', "'%".$search."%'"))
            ->orderBy('Name', 'ASC')
            ->addOrderBy('Id', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param Training $training
     * @param int $offset
     * @return Paginator
     */
    public function getTrainingAttendances(Training $training, int $offset): Paginator
    {
        $qb = $this->createQueryBuilder('a');

        $qb->where($qb->expr()->eq('a.training', $training->getTrainingId()))
            ->orderBy('a.training_attendance_id', 'DESC')
            ->setMaxResults($this->attendancesPerPage)
            ->setFirstResult($offset)
            ->getQuery();

        return new Paginator($qb);
    }
}
