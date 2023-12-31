<?php
// src/Repository/TrainingRepository.php
namespace App\Repository;

use App\Entity\Training;
use App\Entity\TrainingSession;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Training|null find($id, $lockMode = null, $lockVersion = null)
 * @method Training|null findOneBy(array $criteria, array $orderBy = null)
 * @method Training[]    findAll()
 * @method Training[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrainingRepository extends ServiceEntityRepository
{
    /**
     * TrainingRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Training::class);
    }

    /**
     * @param int $year
     * @return array|null
     */
    public function getTraining(int $year): ?array
    {
        $start = $year . '-01-01';
        $end   = $year . '-12-31';

        $qb = $this->createQueryBuilder('t');

        return $qb->innerJoin(TrainingSession::class, 's', 'WITH', $qb->expr()->eq('t.trainingId', 's.trainingSessionTraining'))
            ->where($qb->expr()->gte('s.trainingSessionDate', "'".$start."'"))
            ->andWhere($qb->expr()->lte('s.trainingSessionDate', "'".$end."'"))
            ->groupBy('t.trainingId')
            ->orderBy('s.trainingSessionDate', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
