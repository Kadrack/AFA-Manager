<?php
// src/Entity/TrainingSession.php
namespace App\Entity;

use App\Repository\TrainingSessionRepository;

use App\Service\ListData;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TrainingSession
 */
#[ORM\Table(name: 'trainingSession')]
#[ORM\Entity(repositoryClass: TrainingSessionRepository::class)]
class TrainingSession
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $trainingSessionId;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $trainingSessionDate;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTime $trainingSessionStart;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTime $trainingSessionEnd;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $trainingSessionDuration;

    /**
     * @var Training|null
     */
    #[ORM\ManyToOne(targetEntity: Training::class, cascade: ['persist'], inversedBy: 'trainingSessions')]
    #[ORM\JoinColumn(name: 'trainingSession_join_training', referencedColumnName: 'trainingId', nullable: true)]
    private ?Training $trainingSessionTraining;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'trainingSessionAttendanceTrainingSession', targetEntity: TrainingSessionAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $trainingSessionAttendances;

    /**
     * TrainingSession constructor.
     */
    public function __construct()
    {
        $this->trainingSessionAttendances = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTrainingSessionId(): int
    {
        return $this->trainingSessionId;
    }

    /**
     * @param int $trainingSessionId
     *
     * @return $this
     */
    public function setTrainingSessionId(int $trainingSessionId): self
    {
        $this->trainingSessionId = $trainingSessionId;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingSessionDate(): ?DateTime
    {
        return $this->trainingSessionDate;
    }

    /**
     * @param DateTime|null $trainingSessionDate
     *
     * @return $this
     */
    public function setTrainingSessionDate(?DateTime $trainingSessionDate): self
    {
        $this->trainingSessionDate = $trainingSessionDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingSessionStart(): ?DateTime
    {
        return $this->trainingSessionStart;
    }

    /**
     * @param DateTime|null $trainingSessionStart
     *
     * @return $this
     */
    public function setTrainingSessionStart(?DateTime $trainingSessionStart): self
    {
        $this->trainingSessionStart = $trainingSessionStart;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingSessionEnd(): ?DateTime
    {
        return $this->trainingSessionEnd;
    }

    /**
     * @param DateTime|null $trainingSessionEnd
     *
     * @return $this
     */
    public function setTrainingSessionEnd(?DateTime $trainingSessionEnd): self
    {
        $this->trainingSessionEnd = $trainingSessionEnd;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingSessionDuration(): ?int
    {
        return $this->trainingSessionDuration;
    }

    /**
     * @param int|null $trainingSessionDuration
     *
     * @return $this
     */
    public function setTrainingSessionDuration(?int $trainingSessionDuration): self
    {
        $this->trainingSessionDuration = $trainingSessionDuration;

        return $this;
    }

    /**
     * @return Training|null
     */
    public function getTrainingSessionTraining(): ?Training
    {
        return $this->trainingSessionTraining;
    }

    /**
     * @param Training|null $trainingSessionTraining
     *
     * @return $this
     */
    public function setTrainingSessionTraining(?Training $trainingSessionTraining): self
    {
        $this->trainingSessionTraining = $trainingSessionTraining;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTrainingSessionAttendances(): Collection
    {
        return $this->trainingSessionAttendances;
    }

    /**
     * @param TrainingSessionAttendance $training_session_attendances
     * @return $this
     */
    public function addTrainingSessionAttendances(TrainingSessionAttendance $training_session_attendances): self
    {
        if (!$this->trainingSessionAttendances->contains($training_session_attendances)) {
            $this->trainingSessionAttendances[] = $training_session_attendances;
            $training_session_attendances->setTrainingSessionAttendanceTrainingSession($this);
        }

        return $this;
    }

    /**
     * @param TrainingSessionAttendance $training_session_attendances
     * @return $this
     */
    public function removeTrainingSessionAttendances(TrainingSessionAttendance $training_session_attendances): self
    {
        if ($this->trainingSessionAttendances->contains($training_session_attendances)) {
            $this->trainingSessionAttendances->removeElement($training_session_attendances);
            // set the owning side to null (unless already changed)
            if ($training_session_attendances->getTrainingSessionAttendanceTrainingSession() === $this) {
                $training_session_attendances->setTrainingSessionAttendanceTrainingSession(null);
            }
        }

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return int|string|null
     */
    public function getTrainingSessionChoiceName(): int|string|null
    {
        $list_data = new ListData();

        if ($this->trainingSessionStart == null)
        {

            return $this->trainingSessionDuration / 60 . 'h';

        }

        return $list_data->getDay($this->trainingSessionDate->format('N')).' '.$this->trainingSessionStart->format('H').'h - '.$this->trainingSessionEnd->format('H').'h ('.$this->trainingSessionDuration / 60 . 'h)';
    }
}
