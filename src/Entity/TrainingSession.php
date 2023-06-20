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
#[ORM\Table(name: 'training_session')]
#[ORM\Entity(repositoryClass: TrainingSessionRepository::class)]
class TrainingSession
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $training_session_id;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $training_session_date;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTime $training_session_starting_hour;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTime $training_session_ending_hour;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $training_session_duration;

    /**
     * @var Training|null
     */
    #[ORM\ManyToOne(targetEntity: Training::class, cascade: ['persist'], inversedBy: 'training_sessions')]
    #[ORM\JoinColumn(name: 'training_join_training_session', referencedColumnName: 'training_id', nullable: true)]
    private ?Training $training;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'training_session', targetEntity: TrainingSessionAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $training_session_attendances;

    /**
     * TrainingSession constructor.
     */
    public function __construct()
    {
        $this->training_session_attendances = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTrainingSessionId(): int
    {
        return $this->training_session_id;
    }

    /**
     * @param int $training_session_id
     * @return $this
     */
    public function setTrainingSessionId(int $training_session_id): self
    {
        $this->training_session_id = $training_session_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingSessionDate(): ?DateTime
    {
        return $this->training_session_date;
    }

    /**
     * @param DateTime|null $training_session_date
     * @return $this
     */
    public function setTrainingSessionDate(?DateTime $training_session_date): self
    {
        $this->training_session_date = $training_session_date;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingSessionStartingHour(): ?DateTime
    {
        return $this->training_session_starting_hour;
    }

    /**
     * @param DateTime|null $training_session_starting_hour
     * @return $this
     */
    public function setTrainingSessionStartingHour(?DateTime $training_session_starting_hour): self
    {
        $this->training_session_starting_hour = $training_session_starting_hour;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingSessionEndingHour(): ?DateTime
    {
        return $this->training_session_ending_hour;
    }

    /**
     * @param DateTime|null $training_session_ending_hour
     * @return $this
     */
    public function setTrainingSessionEndingHour(?DateTime $training_session_ending_hour): self
    {
        $this->training_session_ending_hour = $training_session_ending_hour;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTrainingSessionDuration(): ?int
    {
        return $this->training_session_duration;
    }

    /**
     * @param int|null $training_session_duration
     * @return $this
     */
    public function setTrainingSessionDuration(?int $training_session_duration): self
    {
        $this->training_session_duration = $training_session_duration;

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
     * @return Collection
     */
    public function getTrainingSessionAttendances(): Collection
    {
        return $this->training_session_attendances;
    }

    /**
     * @param TrainingSessionAttendance $training_session_attendances
     * @return $this
     */
    public function addTrainingSessionAttendances(TrainingSessionAttendance $training_session_attendances): self
    {
        if (!$this->training_session_attendances->contains($training_session_attendances)) {
            $this->training_session_attendances[] = $training_session_attendances;
            $training_session_attendances->setTrainingSession($this);
        }

        return $this;
    }

    /**
     * @param TrainingSessionAttendance $training_session_attendances
     * @return $this
     */
    public function removeTrainingSessionAttendances(TrainingSessionAttendance $training_session_attendances): self
    {
        if ($this->training_session_attendances->contains($training_session_attendances)) {
            $this->training_session_attendances->removeElement($training_session_attendances);
            // set the owning side to null (unless already changed)
            if ($training_session_attendances->getTrainingSession() === $this) {
                $training_session_attendances->setTrainingSession(null);
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

        if ($this->training_session_starting_hour == null)
        {

            return $this->training_session_duration / 60 . 'h';

        }

        return $list_data->getDay($this->training_session_date->format('N')).' '.$this->training_session_starting_hour->format('H').'h - '.$this->training_session_ending_hour->format('H').'h ('.$this->training_session_duration / 60 . 'h)';
    }
}
