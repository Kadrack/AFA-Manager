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
    private int $titleId;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $titleDate;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $titleRank;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $titleCertificate;

    /**
     * @var Member
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberTitles')]
    #[ORM\JoinColumn(name: 'title_join_member', referencedColumnName: 'memberId', nullable: false)]
    private Member $titleMember;

    /**
     * @return int
     */
    public function getTitleId(): int
    {
        return $this->titleId;
    }

    /**
     * @param int $titleId
     *
     * @return $this
     */
    public function setTitleId(int $titleId): self
    {
        $this->titleId = $titleId;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTitleDate(): ?DateTime
    {
        return $this->titleDate;
    }

    /**
     * @param DateTime $titleDate
     *
     * @return $this
     */
    public function setTitleDate(DateTime $titleDate): self
    {
        $this->titleDate = $titleDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getTitleRank(): int
    {
        return $this->titleRank;
    }

    /**
     * @param int $titleRank
     *
     * @return $this
     */
    public function setTitleRank(int $titleRank): self
    {
        $this->titleRank = $titleRank;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitleCertificate(): ?string
    {
        return $this->titleCertificate;
    }

    /**
     * @param string|null $titleCertificate
     *
     * @return $this
     */
    public function setTitleCertificate(?string $titleCertificate): self
    {
        $this->titleCertificate = $titleCertificate;

        return $this;
    }

    /**
     * @return Member
     */
    public function getTitleMember(): Member
    {
        return $this->titleMember;
    }

    /**
     * @param Member $titleMember
     *
     * @return $this
     */
    public function setTitleMember(Member $titleMember): self
    {
        $this->titleMember = $titleMember;

        return $this;
    }
}
