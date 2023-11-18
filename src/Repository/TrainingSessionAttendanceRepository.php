<?php
// src/Repository/TrainingSessionAttendanceRepository.php
namespace App\Repository;

use App\Entity\TrainingSessionAttendance;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\DBAL\Exception;
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
     * @param array $ids
     *
     * @return array|null
     * @throws Exception
     */
    public function getClubTrainingTotal(array $ids): ?array
    {
        $query = 'SELECT m.memberId as Id, sum(s.trainingSessionDuration) as Total
                    FROM trainingSessionAttendance sa
                    INNER JOIN trainingSession s ON s.trainingSessionId = sa.trainingSessionAttendance_join_trainingSession
                    INNER JOIN trainingAttendance a ON a.trainingAttendanceId = sa.trainingSessionAttendance_join_trainingAttendance
                    INNER JOIN member m ON m.memberId = a.trainingAttendance_join_member
                    WHERE ';

        $start = true;

        foreach ($ids as $member)
        {
            if (is_null($member['GradeDate']))
            {
                continue;
            }

            $date = $member['GradeDate']->format('Y-m-d');

            if ($start)
            {
                $start = false;
            }
            else
            {
                $query = $query . " OR ";
            }

            $id = $member['Id'];

            $query = $query . "(m.memberId = $id AND s.trainingSessionDate > '$date')";
        }

        $query = $query . " AND a.trainingAttendanceStatus = 1 GROUP BY Id";

        return $this->getEntityManager()->getConnection()->executeQuery($query)->fetchAllAssociative();
    }
}
