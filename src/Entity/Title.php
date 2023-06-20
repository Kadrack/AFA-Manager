<?php
// src/Entity/Title.php
namespace App\Entity;

use App\Repository\TitleRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class GradeTitle
 */
#[ORM\Table(name: 'title')]
#[ORM\Entity(repositoryClass: TitleRepository::class)]
class Title
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $title_id;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $title_date;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $title_rank;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $title_certificate;

    /**
     * @var Member
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_titles')]
    #[ORM\JoinColumn(name: 'title_join_member', referencedColumnName: 'member_id', nullable: false)]
    private Member $title_member;

    /**
     * @return int
     */
    public function getTitleId(): int
    {
        return $this->title_id;
    }

    /**
     * @param int $title_id
     * @return $this
     */
    public function setTitleId(int $title_id): self
    {
        $this->title_id = $title_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTitleDate(): ?DateTime
    {
        return $this->title_date;
    }

    /**
     * @param DateTime $title_date
     * @return $this
     */
    public function setTitleDate(DateTime $title_date): self
    {
        $this->title_date = $title_date;

        return $this;
    }

    /**
     * @return int
     */
    public function getTitleRank(): int
    {
        return $this->title_rank;
    }

    /**
     * @param int $title_rank
     * @return $this
     */
    public function setTitleRank(int $title_rank): self
    {
        $this->title_rank = $title_rank;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleCertificate(): ?string
    {
        return $this->title_certificate;
    }

    /**
     * @param string|null $title_certificate
     * @return $this
     */
    public function setTitleCertificate(?string $title_certificate): self
    {
        $this->title_certificate = $title_certificate;

        return $this;
    }

    /**
     * @return Member
     */
    public function getTitleMember(): Member
    {
        return $this->title_member;
    }

    /**
     * @param Member $title_member
     * @return $this
     */
    public function setTitleMember(Member $title_member): self
    {
        $this->title_member = $title_member;

        return $this;
    }
}
