<?php
// src/Entity/MemberLicence.php
namespace App\Entity;

use App\Repository\MemberLicenceRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MemberLicence
 */
#[ORM\Table(name: 'memberLicence')]
#[ORM\Entity(repositoryClass: MemberLicenceRepository::class)]
class MemberLicence
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $memberLicenceId;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $memberLicenceUpdate;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $memberLicenceDeadline;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $memberLicencePaymentDate;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $memberLicencePaymentValue;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $memberLicencePaymentUpdate;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $memberLicencePrintoutCreation;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $memberLicencePrintoutDone;

    /**
     * @var Club|null
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'memberLicence_join_club', referencedColumnName: 'clubId', nullable: true)]
    private ?Club $memberLicenceClub;

    /**
     * @var Member
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberLicences')]
    #[ORM\JoinColumn(name: 'memberLicence_join_member', referencedColumnName: 'memberId', nullable: false)]
    private Member $memberLicenceMember;

    /**
     * @return int
     */
    public function getMemberLicenceId(): int
    {
        return $this->memberLicenceId;
    }

    /**
     * @param int $memberLicenceId
     *
     * @return $this
     */
    public function setMemberLicenceId(int $memberLicenceId): self
    {
        $this->memberLicenceId = $memberLicenceId;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getMemberLicenceUpdate(bool $format = false): DateTime|string|null
    {
        return $format ? $this->memberLicenceUpdate->format('d/m/Y') : $this->memberLicenceUpdate;
    }

    /**
     * @param DateTime|null $memberLicenceUpdate
     *
     * @return $this
     */
    public function setMemberLicenceUpdate(?DateTime $memberLicenceUpdate): self
    {
        $this->memberLicenceUpdate = $memberLicenceUpdate;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getMemberLicenceDeadline(bool $format = false): DateTime|string|null
    {
        if (is_null($this->memberLicenceDeadline))
        {
            return $format ? '--/--/----' : null;
        }

        return $format ? $this->memberLicenceDeadline->format('d/m/Y') : $this->memberLicenceDeadline;
    }

    /**
     * @param DateTime|null $memberLicenceDeadline
     *
     * @return $this
     */
    public function setMemberLicenceDeadline(?DateTime $memberLicenceDeadline): self
    {
        $this->memberLicenceDeadline = $memberLicenceDeadline;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getMemberLicencePaymentDate(bool $format = false): DateTime|string|null
    {
        if (is_null($this->memberLicencePaymentDate))
        {
            return $format ? 'En attente' : null;
        }

        return $format ? $this->memberLicencePaymentDate->format('d/m/Y') : $this->memberLicencePaymentDate;
    }

    /**
     * @param DateTime|null $memberLicencePaymentDate
     *
     * @return $this
     */
    public function setMemberLicencePaymentDate(?DateTime $memberLicencePaymentDate): self
    {
        $this->memberLicencePaymentDate = $memberLicencePaymentDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMemberLicencePaymentValue(): ?int
    {
        return $this->memberLicencePaymentValue;
    }

    /**
     * @param int|null $memberLicencePaymentValue
     *
     * @return $this
     */
    public function setMemberLicencePaymentValue(?int $memberLicencePaymentValue): self
    {
        $this->memberLicencePaymentValue = $memberLicencePaymentValue;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getMemberLicencePaymentUpdate(bool $format = false): DateTime|string|null
    {
        if (is_null($this->memberLicencePaymentUpdate))
        {
            return $format ? '--/--/----' : null;
        }
        return $format ? $this->memberLicencePaymentUpdate->format('d/m/Y') : $this->memberLicencePaymentUpdate;
    }

    /**
     * @param DateTime|null $memberLicencePaymentUpdate
     *
     * @return $this
     */
    public function setMemberLicencePaymentUpdate(?DateTime $memberLicencePaymentUpdate): self
    {
        $this->memberLicencePaymentUpdate = $memberLicencePaymentUpdate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberLicencePrintoutCreation(): ?DateTime
    {
        return $this->memberLicencePrintoutCreation;
    }

    /**
     * @param DateTime|null $memberLicencePrintoutCreation
     *
     * @return $this
     */
    public function setMemberLicencePrintoutCreation(?DateTime $memberLicencePrintoutCreation): self
    {
        $this->memberLicencePrintoutCreation = $memberLicencePrintoutCreation;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberLicencePrintoutDone(): ?DateTime
    {
        return $this->memberLicencePrintoutDone;
    }

    /**
     * @param DateTime|null $memberLicencePrintoutDone
     *
     * @return $this
     */
    public function setMemberLicencePrintoutDone(?DateTime $memberLicencePrintoutDone): self
    {
        $this->memberLicencePrintoutDone = $memberLicencePrintoutDone;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getMemberLicenceClub(): ?Club
    {
        return $this->memberLicenceClub;
    }

    /**
     * @param Club|null $memberLicenceClub
     *
     * @return $this
     */
    public function setMemberLicenceClub(?Club $memberLicenceClub): self
    {
        $this->memberLicenceClub = $memberLicenceClub;

        return $this;
    }

    /**
     * @return Member
     */
    public function getMemberLicenceMember(): Member
    {
        return $this->memberLicenceMember;
    }

    /**
     * @param Member $memberLicenceMember
     *
     * @return $this
     */
    public function setMemberLicenceMember(Member $memberLicenceMember): self
    {
        $this->memberLicenceMember = $memberLicenceMember;

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return int
     */
    public function getMemberId(): int
    {
        return $this->memberLicenceMember->getMemberId();
    }

    /**
     * @return string
     */
    public function getMemberFirstname(): string
    {
        return $this->memberLicenceMember->getMemberFirstname();
    }

    /**
     * @return string
     */
    public function getMemberName(): string
    {
        return $this->memberLicenceMember->getMemberName();
    }
}
