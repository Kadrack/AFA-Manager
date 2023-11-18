<?php
// src/Entity/NewsletterSubscription.php
namespace App\Entity;

use App\Repository\NewsletterSubscriptionRepository;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class NewsletterSubscription
 */
#[ORM\Table(name: 'newsletterSubscription')]
#[ORM\Entity(repositoryClass: NewsletterSubscriptionRepository::class)]
class NewsletterSubscription
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $newsletterSubscriptionId;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $newsletterSubscriptionEmail;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $newsletterSubscriptionUniqueId;

    public function __construct()
    {
        $this->setNewsletterSubscriptionUniqueId(md5(microtime()));
    }

    /**
     * @return int
     */
    public function getNewsletterSubscriptionId(): int
    {
        return $this->newsletterSubscriptionId;
    }

    /**
     * @param int $newsletterSubscriptionId
     *
     * @return $this
     */
    public function setNewsletterSubscriptionId(int $newsletterSubscriptionId): self
    {
        $this->newsletterSubscriptionId = $newsletterSubscriptionId;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewsletterSubscriptionEmail(): string
    {
        return $this->newsletterSubscriptionEmail;
    }

    /**
     * @param string $newsletterSubscriptionEmail
     *
     * @return $this
     */
    public function setNewsletterSubscriptionEmail(string $newsletterSubscriptionEmail): self
    {
        $this->newsletterSubscriptionEmail = $newsletterSubscriptionEmail;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewsletterSubscriptionUniqueId(): string
    {
        return $this->newsletterSubscriptionUniqueId;
    }

    /**
     * @param string $newsletterSubscriptionUniqueId
     *
     * @return $this
     */
    public function setNewsletterSubscriptionUniqueId(string $newsletterSubscriptionUniqueId): self
    {
        $this->newsletterSubscriptionUniqueId = $newsletterSubscriptionUniqueId;

        return $this;
    }
}
