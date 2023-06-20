<?php
// src/Entity/MemberLicence.php
namespace App\Entity;

use App\Repository\MemberLicenceRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class MemberLicence
 */
#[ORM\Table(name: 'member_licence')]
#[ORM\Entity(repositoryClass: MemberLicenceRepository::class)]
class MemberLicence
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $member_licence_id;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $member_licence_update;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $member_licence_deadline;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $member_licence_payment_date;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $member_licence_payment_value;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $member_licence_payment_update;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $member_licence_printout_creation;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $member_licence_printout_done;

    /**
     * @var Club|null
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'club_licences')]
    #[ORM\JoinColumn(name: 'member_licence_join_club', referencedColumnName: 'club_id', nullable: true)]
    private ?Club $member_licence_club;

    /**
     * @var Member
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_licences')]
    #[ORM\JoinColumn(name: 'member_licence_join_member', referencedColumnName: 'member_id', nullable: false)]
    private Member $member_licence;

    /**
     * @return int
     */
    public function getMemberLicenceId(): int
    {
        return $this->member_licence_id;
    }

    /**
     * @param int $member_licence_id
     * @return $this
     */
    public function setMemberLicenceId(int $member_licence_id): self
    {
        $this->member_licence_id = $member_licence_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberLicenceUpdate(): ?DateTime
    {
        return $this->member_licence_update;
    }

    /**
     * @param DateTime|null $member_licence_update
     * @return $this
     */
    public function setMemberLicenceUpdate(?DateTime $member_licence_update): self
    {
        $this->member_licence_update = $member_licence_update;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberLicenceDeadline(): ?DateTime
    {
        return $this->member_licence_deadline;
    }

    /**
     * @param DateTime|null $member_licence_deadline
     * @return $this
     */
    public function setMemberLicenceDeadline(?DateTime $member_licence_deadline): self
    {
        $this->member_licence_deadline = $member_licence_deadline;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberLicencePaymentDate(): ?DateTime
    {
        return $this->member_licence_payment_date;
    }

    /**
     * @param DateTime|null $member_licence_payment_date
     * @return $this
     */
    public function setMemberLicencePaymentDate(?DateTime $member_licence_payment_date): self
    {
        $this->member_licence_payment_date = $member_licence_payment_date;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getMemberLicencePaymentValue(): ?int
    {
        return $this->member_licence_payment_value;
    }

    /**
     * @param int|null $member_licence_payment_value
     * @return $this
     */
    public function setMemberLicencePaymentValue(?int $member_licence_payment_value): self
    {
        $this->member_licence_payment_value = $member_licence_payment_value;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberLicencePaymentUpdate(): ?DateTime
    {
        return $this->member_licence_payment_update;
    }

    /**
     * @param DateTime|null $member_licence_payment_update
     * @return $this
     */
    public function setMemberLicencePaymentUpdate(?DateTime $member_licence_payment_update): self
    {
        $this->member_licence_payment_update = $member_licence_payment_update;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberLicencePrintoutCreation(): ?DateTime
    {
        return $this->member_licence_printout_creation;
    }

    /**
     * @param DateTime|null $member_licence_printout_creation
     * @return $this
     */
    public function setMemberLicencePrintoutCreation(?DateTime $member_licence_printout_creation): self
    {
        $this->member_licence_printout_creation = $member_licence_printout_creation;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberLicencePrintoutDone(): ?DateTime
    {
        return $this->member_licence_printout_done;
    }

    /**
     * @param DateTime|null $member_licence_printout_done
     * @return $this
     */
    public function setMemberLicencePrintoutDone(?DateTime $member_licence_printout_done): self
    {
        $this->member_licence_printout_done = $member_licence_printout_done;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getMemberLicenceClub(): ?Club
    {
        return $this->member_licence_club;
    }

    /**
     * @param Club|null $member_licence_club
     * @return $this
     */
    public function setMemberLicenceClub(?Club $member_licence_club): self
    {
        $this->member_licence_club = $member_licence_club;

        return $this;
    }

    /**
     * @return Member
     */
    public function getMemberLicence(): Member
    {
        return $this->member_licence;
    }

    /**
     * @param Member $member_licence
     * @return $this
     */
    public function setMemberLicence(Member $member_licence): self
    {
        $this->member_licence = $member_licence;

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
        return $this->member_licence->getMemberId();
    }

    /**
     * @return string
     */
    public function getMemberFirstname(): string
    {
        return $this->member_licence->getMemberFirstname();
    }

    /**
     * @return string
     */
    public function getMemberName(): string
    {
        return $this->member_licence->getMemberName();
    }
}
