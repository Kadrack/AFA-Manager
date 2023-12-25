<?php
// src/Repository/QrCodesRepository.php
namespace App\Repository;

use App\Entity\QrCodes;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method QrCodes|null find($id, $lockMode = null, $lockVersion = null)
 * @method QrCodes|null findOneBy(array $criteria, array $orderBy = null)
 * @method QrCodes[]    findAll()
 * @method QrCodes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QrCodesRepository extends ServiceEntityRepository
{
    /**
     * MemberLicenceRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QrCodes::class);
    }
}
