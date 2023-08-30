<?php
// src/Entity/Training.php
namespace App\Entity;

use App\Repository\TrainingRepository;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Training
 */
#[ORM\Table(name: 'training')]
#[ORM\Entity(repositoryClass: TrainingRepository::class)]
class Training
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $training_id;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $training_name;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $training_type;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $training_status;

    /**
     * @var TrainingSession|null
     */
    #[Assert\Type('App\Entity\TrainingSession')]
    #[Assert\Valid]
    protected ?TrainingSession $session;

    /**
     * @var Club|null
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'club_trainings')]
    #[ORM\JoinColumn(name: 'training_join_club', referencedColumnName: 'club_id', nullable: true)]
    private ?Club $training_club;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'training', targetEntity: TrainingAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['training_attendance_id' => 'DESC'])]
    private ArrayCollection|Collection|null $training_attendances;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'training', targetEntity: TrainingSession::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['training_session_date' => 'ASC', 'training_session_starting_hour' => 'ASC'])]
    private ArrayCollection|Collection|null $training_sessions;

    /**
     * Training constructor.
     */
    public function __construct()
    {
        $this->training_attendances = new ArrayCollection();
        $this->training_sessions    = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTrainingId(): int
    {
        return $this->training_id;
    }

    /**
     * @param int $training_id
     * @return $this
     */
    public function setTrainingId(int $training_id): self
    {
        $this->training_id = $training_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingName(): ?string
    {
        return $this->training_name;
    }

    /**
     * @param string|null $training_name
     * @return $this
     */
    public function setTrainingName(?string $training_name): self
    {
        $this->training_name = $training_name;

        return $this;
    }

    /**
     * @return int
     */
    public function getTrainingType(): int
    {
        return $this->training_type;
    }

    /**
     * @param int $training_type
     * @return $this
     */
    public function setTrainingType(int $training_type): self
    {
        $this->training_type = $training_type;

        return $this;
    }

    /**
     * @return int
     */
    public function getTrainingStatus(): int
    {
        return $this->training_status;
    }

    /**
     * @param int $training_status
     * @return $this
     */
    public function setTrainingStatus(int $training_status): self
    {
        $this->training_status = $training_status;

        return $this;
    }

    /**
     * @return TrainingSession|null
     */
    public function getSession(): ?TrainingSession
    {
        return $this->session;
    }

    /**
     * @param TrainingSession|null $session
     * @return Training
     */
    public function setSession(?TrainingSession $session = null): self
    {
        $this->session = $session;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getTrainingClub(): ?Club
    {
        return $this->training_club;
    }

    /**
     * @param Club|null $training_club
     * @return $this
     */
    public function setTrainingClub(?Club $training_club): self
    {
        $this->training_club = $training_club;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTrainingAttendances(): Collection
    {
        return $this->training_attendances;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
    public function addTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if (!$this->training_attendances->contains($trainingAttendance)) {
            $this->training_attendances[] = $trainingAttendance;
            $trainingAttendance->setTraining($this);
        }

        return $this;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
    public function removeTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if ($this->training_attendances->contains($trainingAttendance)) {
            $this->training_attendances->removeElement($trainingAttendance);
            // set the owning side to null (unless already changed)
            if ($trainingAttendance->getTraining() === $this) {
                $trainingAttendance->setTraining(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTrainingSessions(): Collection
    {
        return $this->training_sessions;
    }

    /**
     * @param TrainingSession $trainingSession
     * @return $this
     */
    public function addTrainingSessions(TrainingSession $trainingSession): self
    {
        if (!$this->training_sessions->contains($trainingSession)) {
            $this->training_sessions[] = $trainingSession;
            $trainingSession->setTraining($this);
        }

        return $this;
    }

    /**
     * @param TrainingSession $trainingSession
     * @return $this
     */
    public function removeTrainingSessions(TrainingSession $trainingSession): self
    {
        if ($this->training_sessions->contains($trainingSession)) {
            $this->training_sessions->removeElement($trainingSession);
            // set the owning side to null (unless already changed)
            if ($trainingSession->getTraining() === $this) {
                $trainingSession->setTraining(null);
            }
        }

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return array
     */
    public function getTrainingPaymentsTotal(): array
    {
        $total['Cash']      = 0;
        $total['Card']      = 0;
        $total['Transfert'] = 0;

        foreach ($this->getTrainingAttendances() as $payment)
        {
            $total['Cash']      = $total['Cash'] + $payment->getTrainingAttendancePaymentCash();
            $total['Card']      = $total['Card'] + $payment->getTrainingAttendancePaymentCard();
            $total['Transfert'] = $total['Transfert'] + $payment->getTrainingAttendancePaymentTransfert();
        }

        return $total;
    }

    /**
     * @return array
     */
    public function getTrainingAttendanceTotalDetail(): array
    {
        $total['Member']  = 0;
        $total['Foreign'] = 0;

        foreach ($this->getTrainingAttendances() as $attendance)
        {
            is_null($attendance->getTrainingAttendanceMember()) ? $total['Foreign']++ : $total['Member']++;
        }

        return $total;
    }

    /**
     * @return array
     */
    public function getTrainingAttendancesTotal(): array
    {
        $count['AFA']     = 0;
        $count['Foreign'] = 0;
        $count['Total']   = 0;

        foreach ($this->getTrainingAttendances() as $attendance)
        {
            if (is_null($attendance->getTrainingAttendanceMember()))
            {
                $count['Foreign']++;
            }
            else
            {
                $count['AFA']++;
            }

            $count['Total']++;
        }

        return $count;
    }

    /**
     * @return int
     */
    public function getTrainingSessionsTotal(): int
    {
        return count($this->training_sessions);
    }

    /**
     * @return int
     */
    public function getTrainingSessionsDurationTotal(): int
    {
        $total = 0;

        foreach ($this->training_sessions as $session)
        {
            $total = $total + $session->getTrainingSessionDuration();
        }

        return $total;
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingFirstDate(): ?DateTime
    {
        return $this->training_sessions[0]->getTrainingSessionDate();
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingLastDate(): ?DateTime
    {
        return $this->training_sessions[$this->getTrainingSessionsTotal()-1]->getTrainingSessionDate();
    }
}
