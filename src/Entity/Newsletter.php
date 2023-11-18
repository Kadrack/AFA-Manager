<?php
// src/Entity/Newsletter.php
namespace App\Entity;

use App\Repository\NewsletterRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Newsletter
 */
#[ORM\Table(name: 'newsletter')]
#[ORM\Entity(repositoryClass: NewsletterRepository::class)]
class Newsletter
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $newsletterId;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $newsletterDate;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $newsletterTitle;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    private string $newsletterText;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $newsletterView;

    /**
     * @return int
     */
    public function getNewsletterId(): int
    {
        return $this->newsletterId;
    }

    /**
     * @param int $newsletterId
     *
     * @return $this
     */
    public function setNewsletterId(int $newsletterId): self
    {
        $this->newsletterId = $newsletterId;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getNewsletterDate(): ?DateTime
    {
        return $this->newsletterDate;
    }

    /**
     * @param ?DateTime $newsletterDate
     *
     * @return $this
     */
    public function setNewsletterDate(?DateTime $newsletterDate): self
    {
        $this->newsletterDate = $newsletterDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewsletterTitle(): string
    {
        return $this->newsletterTitle;
    }

    /**
     * @param string $newsletterTitle
     *
     * @return $this
     */
    public function setNewsletterTitle(string $newsletterTitle): self
    {
        $this->newsletterTitle = $newsletterTitle;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewsletterText(): string
    {
        return $this->newsletterText;
    }

    /**
     * @param string $newsletterText
     *
     * @return $this
     */
    public function setNewsletterText(string $newsletterText): self
    {
        $this->newsletterText = $newsletterText;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNewsletterView(): ?string
    {
        return $this->newsletterView;
    }

    /**
     * @param string|null $newsletterView
     *
     * @return $this
     */
    public function setNewsletterView(?string $newsletterView): self
    {
        $this->newsletterView = $newsletterView;

        return $this;
    }
}
