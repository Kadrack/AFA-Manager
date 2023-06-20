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
    private int $newsletter_id;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $newsletter_date;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $newsletter_title;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    private string $newsletter_text;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $newsletter_view;

    /**
     * @return int
     */
    public function getNewsletterId(): int
    {
        return $this->newsletter_id;
    }

    /**
     * @param int $newsletter_id
     * @return $this
     */
    public function setNewsletterId(int $newsletter_id): self
    {
        $this->newsletter_id = $newsletter_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getNewsletterDate(): ?DateTime
    {
        return $this->newsletter_date;
    }

    /**
     * @param ?DateTime $newsletter_date
     * @return $this
     */
    public function setNewsletterDate(?DateTime $newsletter_date): self
    {
        $this->newsletter_date = $newsletter_date;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewsletterTitle(): string
    {
        return $this->newsletter_title;
    }

    /**
     * @param string $newsletter_title
     * @return $this
     */
    public function setNewsletterTitle(string $newsletter_title): self
    {
        $this->newsletter_title = $newsletter_title;

        return $this;
    }

    /**
     * @return string
     */
    public function getNewsletterText(): string
    {
        return $this->newsletter_text;
    }

    /**
     * @param string $newsletter_text
     * @return $this
     */
    public function setNewsletterText(string $newsletter_text): self
    {
        $this->newsletter_text = $newsletter_text;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNewsletterView(): ?string
    {
        return $this->newsletter_view;
    }

    /**
     * @param string|null $newsletter_view
     * @return $this
     */
    public function setNewsletterView(?string $newsletter_view): self
    {
        $this->newsletter_view = $newsletter_view;

        return $this;
    }
}
