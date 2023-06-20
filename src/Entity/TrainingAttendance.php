<?php
// src/Entity/TrainingAttendance.php
namespace App\Entity;

use App\Repository\TrainingAttendanceRepository;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TrainingAttendance
 */
#[ORM\Table(name: 'training_attendance')]
#[ORM\Entity(repositoryClass: TrainingAttendanceRepository::class)]
class TrainingAttendance
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $training_attendance_id;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $training_attendance_name;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $training_attendance_sex;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $training_attendance_country;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
    private ?int $training_attendance_payment_cash = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
    private ?int $training_attendance_payment_card = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
    private ?int $training_attendance_payment_transfert = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $training_attendance_payment_discount;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $training_attendance_payment_validity;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private int $training_attendance_status = 1;

    /**
     * @var Training|null
     */
    #[ORM\ManyToOne(targetEntity: Training::class, cascade: ['persist'], inversedBy: 'training_attendances')]
    #[ORM\JoinColumn(name: 'training_attendance_join_training', referencedColumnName: 'training_id', nullable: true)]
    private ?Training $training;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_training_attendances')]
    #[ORM\JoinColumn(name: 'training_attendance_join_member', referencedColumnName: 'member_id', nullable: true)]
    private ?Member $training_attendance_member;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'training_session_attendances', targetEntity: TrainingSessionAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $training_attendance_sessions;

    /**
     * TrainingSession constructor.
     */
    public function __construct()
    {
        $this->training_attendance_sessions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTrainingAttendanceId(): int
    {
        return $this->training_attendance_id;
    }

    /**
     * @param int $training_attendance_id
     * @return $this
     */
    public function setTrainingAttendanceId(int $training_attendance_id): self
    {
        $this->training_attendance_id = $training_attendance_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingAttendanceName(): ?string
    {
        return $this->training_attendance_name;
    }

    /**
     * @param string|null $training_attendance_name
     * @return $this
     */
    public function setTrainingAttendanceName(?string $training_attendance_name): self
    {
        $this->training_attendance_name = $training_attendance_name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendanceSex(): ?int
    {
        return $this->training_attendance_sex;
    }

    /**
     * @param int|null $training_attendance_sex
     * @return $this
     */
    public function setTrainingAttendanceSex(?int $training_attendance_sex): self
    {
        $this->training_attendance_sex = $training_attendance_sex;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingAttendanceCountry(): ?string
    {
        return $this->training_attendance_country;
    }

    /**
     * @param string|null $training_attendance_country
     * @return $this
     */
    public function setTrainingAttendanceCountry(?string $training_attendance_country): self
    {
        $this->training_attendance_country = $training_attendance_country;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendancePaymentCash(): ?int
    {
        return $this->training_attendance_payment_cash;
    }

    /**
     * @param int|null $training_attendance_payment_cash
     * @return $this
     */
    public function setTrainingAttendancePaymentCash(?int $training_attendance_payment_cash): self
    {
        $this->training_attendance_payment_cash = $training_attendance_payment_cash;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendancePaymentCard(): ?int
    {
        return $this->training_attendance_payment_card;
    }

    /**
     * @param int|null $training_attendance_payment_card
     * @return $this
     */
    public function setTrainingAttendancePaymentCard(?int $training_attendance_payment_card): self
    {
        $this->training_attendance_payment_card = $training_attendance_payment_card;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendancePaymentTransfert(): ?int
    {
        return $this->training_attendance_payment_transfert;
    }

    /**
     * @param int|null $training_attendance_payment_transfert
     * @return $this
     */
    public function setTrainingAttendancePaymentTransfert(?int $training_attendance_payment_transfert): self
    {
        $this->training_attendance_payment_transfert = $training_attendance_payment_transfert;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendancePaymentDiscount(): ?int
    {
        return $this->training_attendance_payment_discount;
    }

    /**
     * @param int|null $training_attendance_payment_discount
     * @return $this
     */
    public function setTrainingAttendancePaymentDiscount(?int $training_attendance_payment_discount): self
    {
        $this->training_attendance_payment_discount = $training_attendance_payment_discount;

        return $this;
    }

    /**
     * @return int
     */
    public function getTrainingAttendanceStatus(): int
    {
        return $this->training_attendance_status;
    }

    /**
     * @param int $training_attendance_status
     * @return $this
     */
    public function setTrainingAttendanceStatus(int $training_attendance_status): self
    {
        $this->training_attendance_status = $training_attendance_status;

        return $this;
    }

    /**
     * @return Training|null
     */
    public function getTraining(): ?Training
    {
        return $this->training;
    }

    /**
     * @param Training|null $training
     * @return $this
     */
    public function setTraining(?Training $training): self
    {
        $this->training = $training;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getTrainingAttendanceMember(): ?Member
    {
        return $this->training_attendance_member;
    }

    /**
     * @param Member|null $member
     * @return $this
     */
    public function setTrainingAttendanceMember(?Member $member): self
    {
        $this->training_attendance_member = $member;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTrainingAttendanceSessions(): Collection
    {
        return $this->training_attendance_sessions;
    }

    /**
     * @param TrainingSessionAttendance $training_attendance_sessions
     * @return $this
     */
    public function addTrainingAttendanceSessions(TrainingSessionAttendance $training_attendance_sessions): self
    {
        if (!$this->training_attendance_sessions->contains($training_attendance_sessions)) {
            $this->training_attendance_sessions[] = $training_attendance_sessions;
            $training_attendance_sessions->setTrainingSessionAttendances($this);
        }

        return $this;
    }

    /**
     * @param TrainingSessionAttendance $training_attendance_sessions
     * @return $this
     */
    public function removeTrainingAttendanceSessions(TrainingSessionAttendance $training_attendance_sessions): self
    {
        if ($this->training_attendance_sessions->contains($training_attendance_sessions)) {
            $this->training_attendance_sessions->removeElement($training_attendance_sessions);
            // set the owning side to null (unless already changed)
            if ($training_attendance_sessions->getTrainingSessionAttendances() === $this) {
                $training_attendance_sessions->setTrainingSessionAttendances(null);
            }
        }

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return int|null
     */
    public function getTrainingAttendancePaymentTotal(): ?int
    {
        return $this->getTrainingAttendancePaymentCash() + $this->getTrainingAttendancePaymentCard() + $this->getTrainingAttendancePaymentTransfert() + $this->getTrainingAttendancePaymentDiscount();
    }

    /**
     * @return bool
     */
    public function getTrainingAttendanceIsFree(): bool
    {
        if (is_null($this->getTrainingAttendancePaymentCash()) && is_null($this->getTrainingAttendancePaymentCard()) && is_null($this->getTrainingAttendancePaymentTransfert()))
        {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getTrainingAttendanceTotalHour(): int
    {
        $total = 0;

        foreach ($this->training_attendance_sessions as $session)
        {
            $total = $total + $session->getTrainingSessionDuration();
        }

        return $total;
    }
}
