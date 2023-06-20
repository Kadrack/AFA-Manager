<?php
// src/Repository/NewsletterSubscriptionRepository.php
namespace App\Repository;

use App\Entity\NewsletterSubscription;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NewsletterSubscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method NewsletterSubscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method NewsletterSubscription[]    findAll()
 * @method NewsletterSubscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsletterSubscriptionRepository extends ServiceEntityRepository
{
    /**
     * GradeTitleRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NewsletterSubscription::class);
    }

    /**
     * @param int $id
     * @return array|null
     */
    public function getNewsletterSubscriptions(int $id): ?array
    {
        $qb = $this->createQueryBuilder('n');

        return $qb->where($qb->expr()->gte('n.newsletter_subscription_id', $id))
            ->andWhere($qb->expr()->lt('n.newsletter_subscription_id', $id+500))
            ->getQuery()
            ->getArrayResult();
    }

}
