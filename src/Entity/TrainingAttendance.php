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
#[ORM\Table(name: 'trainingAttendance')]
#[ORM\Entity(repositoryClass: TrainingAttendanceRepository::class)]
class TrainingAttendance
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $trainingAttendanceId;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $trainingAttendanceName;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $trainingAttendanceSex;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $trainingAttendanceCountry;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
    private ?int $trainingAttendancePaymentCash = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
    private ?int $trainingAttendancePaymentCard = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
    private ?int $trainingAttendancePaymentTransfert = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $trainingAttendancePaymentDiscount;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private int $trainingAttendanceStatus = 1;

    /**
     * @var Training|null
     */
    #[ORM\ManyToOne(targetEntity: Training::class, cascade: ['persist'], inversedBy: 'trainingAttendances')]
    #[ORM\JoinColumn(name: 'trainingAttendance_join_training', referencedColumnName: 'trainingId', nullable: true)]
    private ?Training $trainingAttendanceTraining;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberTrainingAttendances')]
    #[ORM\JoinColumn(name: 'trainingAttendance_join_member', referencedColumnName: 'memberId', nullable: true)]
    private ?Member $trainingAttendanceMember;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'trainingSessionAttendanceTrainingAttendance', targetEntity: TrainingSessionAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $trainingAttendanceSessions;

    /**
     * TrainingSession constructor.
     */
    public function __construct()
    {
        $this->trainingAttendanceSessions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTrainingAttendanceId(): int
    {
        return $this->trainingAttendanceId;
    }

    /**
     * @param int $trainingAttendanceId
     *
     * @return $this
     */
    public function setTrainingAttendanceId(int $trainingAttendanceId): self
    {
        $this->trainingAttendanceId = $trainingAttendanceId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingAttendanceName(): ?string
    {
        return $this->trainingAttendanceName;
    }

    /**
     * @param string|null $trainingAttendanceName
     *
     * @return $this
     */
    public function setTrainingAttendanceName(?string $trainingAttendanceName): self
    {
        $this->trainingAttendanceName = $trainingAttendanceName;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendanceSex(): ?int
    {
        return $this->trainingAttendanceSex;
    }

    /**
     * @param int|null $trainingAttendanceSex
     *
     * @return $this
     */
    public function setTrainingAttendanceSex(?int $trainingAttendanceSex): self
    {
        $this->trainingAttendanceSex = $trainingAttendanceSex;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingAttendanceCountry(): ?string
    {
        return $this->trainingAttendanceCountry;
    }

    /**
     * @param string|null $trainingAttendanceCountry
     *
     * @return $this
     */
    public function setTrainingAttendanceCountry(?string $trainingAttendanceCountry): self
    {
        $this->trainingAttendanceCountry = $trainingAttendanceCountry;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendancePaymentCash(): ?int
    {
        return $this->trainingAttendancePaymentCash;
    }

    /**
     * @param int|null $trainingAttendancePaymentCash
     *
     * @return $this
     */
    public function setTrainingAttendancePaymentCash(?int $trainingAttendancePaymentCash): self
    {
        $this->trainingAttendancePaymentCash = $trainingAttendancePaymentCash;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendancePaymentCard(): ?int
    {
        return $this->trainingAttendancePaymentCard;
    }

    /**
     * @param int|null $trainingAttendancePaymentCard
     *
     * @return $this
     */
    public function setTrainingAttendancePaymentCard(?int $trainingAttendancePaymentCard): self
    {
        $this->trainingAttendancePaymentCard = $trainingAttendancePaymentCard;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendancePaymentTransfert(): ?int
    {
        return $this->trainingAttendancePaymentTransfert;
    }

    /**
     * @param int|null $trainingAttendancePaymentTransfert
     *
     * @return $this
     */
    public function setTrainingAttendancePaymentTransfert(?int $trainingAttendancePaymentTransfert): self
    {
        $this->trainingAttendancePaymentTransfert = $trainingAttendancePaymentTransfert;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingAttendancePaymentDiscount(): ?int
    {
        return $this->trainingAttendancePaymentDiscount;
    }

    /**
     * @param int|null $trainingAttendancePaymentDiscount
     *
     * @return $this
     */
    public function setTrainingAttendancePaymentDiscount(?int $trainingAttendancePaymentDiscount): self
    {
        $this->trainingAttendancePaymentDiscount = $trainingAttendancePaymentDiscount;

        return $this;
    }

    /**
     * @return int
     */
    public function getTrainingAttendanceStatus(): int
    {
        return $this->trainingAttendanceStatus;
    }

    /**
     * @param int $trainingAttendanceStatus
     *
     * @return $this
     */
    public function setTrainingAttendanceStatus(int $trainingAttendanceStatus): self
    {
        $this->trainingAttendanceStatus = $trainingAttendanceStatus;

        return $this;
    }

    /**
     * @return Training|null
     */
    public function getTrainingAttendanceTraining(): ?Training
    {
        return $this->trainingAttendanceTraining;
    }

    /**
     * @param Training|null $trainingAttendanceTraining
     *
     * @return $this
     */
    public function setTrainingAttendanceTraining(?Training $trainingAttendanceTraining): self
    {
        $this->trainingAttendanceTraining = $trainingAttendanceTraining;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getTrainingAttendanceMember(): ?Member
    {
        return $this->trainingAttendanceMember;
    }

    /**
     * @param Member|null $member
     * @return $this
     */
    public function setTrainingAttendanceMember(?Member $member): self
    {
        $this->trainingAttendanceMember = $member;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTrainingAttendanceSessions(): Collection
    {
        return $this->trainingAttendanceSessions;
    }

    /**
     * @param TrainingSessionAttendance $training_attendance_sessions
     * @return $this
     */
    public function addTrainingAttendanceSessions(TrainingSessionAttendance $training_attendance_sessions): self
    {
        if (!$this->trainingAttendanceSessions->contains($training_attendance_sessions)) {
            $this->trainingAttendanceSessions[] = $training_attendance_sessions;
            $training_attendance_sessions->setTrainingSessionAttendanceTrainingAttendance($this);
        }

        return $this;
    }

    /**
     * @param TrainingSessionAttendance $training_attendance_sessions
     * @return $this
     */
    public function removeTrainingAttendanceSessions(TrainingSessionAttendance $training_attendance_sessions): self
    {
        if ($this->trainingAttendanceSessions->contains($training_attendance_sessions)) {
            $this->trainingAttendanceSessions->removeElement($training_attendance_sessions);
            // set the owning side to null (unless already changed)
            if ($training_attendance_sessions->getTrainingSessionAttendanceTrainingAttendance() === $this) {
                $training_attendance_sessions->setTrainingSessionAttendanceTrainingAttendance(null);
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

        foreach ($this->trainingAttendanceSessions as $session)
        {
            $total = $total + $session->getTrainingSessionDuration();
        }

        return $total;
    }
}
