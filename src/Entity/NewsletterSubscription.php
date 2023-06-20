<?php
// src/Entity/NewsletterSubscription.php
namespace App\Entity;

use App\Repository\NewsletterSubscriptionRepository;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class NewsletterSubscription
 */
#[ORM\Table(name: 'newsletter_subscription')]
#[ORM\Entity(repositoryClass: NewsletterSubscriptionRepository::class)]
class NewsletterSubscription
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $newsletter_subscription_id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $newsletter_subscription_email;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $newsletter_subscription_unique_id;

    public function __construct()
    {
        $this->setNewsletterSubscriptionUniqueId(md5(microtime()));
    }

    /**
     * @return int
     */
    public function getNewsletterSubscriptionId(): int
    {
        return $this->newsletter_subscription_id;
    }

    /**
     * @param int $newsletter_subscription_id
     * @return $this
     */
    public function setNewsletterSubscriptionId(int $newsletter_subscription_id): self
    {
        $this->newsletter_subscription_id = $newsletter_subscription_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewsletterSubscriptionEmail(): string
    {
        return $this->newsletter_subscription_email;
    }

    /**
     * @param string $newsletter_subscription_email
     * @return $this
     */
    public function setNewsletterSubscriptionEmail(string $newsletter_subscription_email): self
    {
        $this->newsletter_subscription_email = $newsletter_subscription_email;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewsletterSubscriptionUniqueId(): string
    {
        return $this->newsletter_subscription_unique_id;
    }

    /**
     * @param string $newsletter_subscription_unique_id
     * @return $this
     */
    public function setNewsletterSubscriptionUniqueId(string $newsletter_subscription_unique_id): self
    {
        $this->newsletter_subscription_unique_id = $newsletter_subscription_unique_id;

        return $this;
    }
}
