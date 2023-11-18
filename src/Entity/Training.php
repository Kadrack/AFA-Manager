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
    private int $trainingId;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $trainingName;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $trainingStatus;

    /**
     * @var TrainingSession|null
     */
    #[Assert\Type('App\Entity\TrainingSession')]
    #[Assert\Valid]
    protected ?TrainingSession $session;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'trainingAttendanceTraining', targetEntity: TrainingAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['trainingAttendanceId' => 'DESC'])]
    private ArrayCollection|Collection|null $trainingAttendances;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'trainingSessionTraining', targetEntity: TrainingSession::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['trainingSessionDate' => 'ASC', 'trainingSessionStart' => 'ASC'])]
    private ArrayCollection|Collection|null $trainingSessions;

    /**
     * Training constructor.
     */
    public function __construct()
    {
        $this->trainingAttendances = new ArrayCollection();
        $this->trainingSessions    = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getTrainingId(): int
    {
        return $this->trainingId;
    }

    /**
     * @param int $trainingId
     *
     * @return $this
     */
    public function setTrainingId(int $trainingId): self
    {
        $this->trainingId = $trainingId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTrainingName(): ?string
    {
        return $this->trainingName;
    }

    /**
     * @param string|null $trainingName
     *
     * @return $this
     */
    public function setTrainingName(?string $trainingName): self
    {
        $this->trainingName = $trainingName;

        return $this;
    }

    /**
     * @return int
     */
    public function getTrainingStatus(): int
    {
        return $this->trainingStatus;
    }

    /**
     * @param int $trainingStatus
     *
     * @return $this
     */
    public function setTrainingStatus(int $trainingStatus): self
    {
        $this->trainingStatus = $trainingStatus;

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
     * @return Collection
     */
    public function getTrainingAttendances(): Collection
    {
        return $this->trainingAttendances;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
    public function addTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if (!$this->trainingAttendances->contains($trainingAttendance)) {
            $this->trainingAttendances[] = $trainingAttendance;
            $trainingAttendance->setTrainingAttendanceTraining($this);
        }

        return $this;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
    public function removeTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if ($this->trainingAttendances->contains($trainingAttendance)) {
            $this->trainingAttendances->removeElement($trainingAttendance);
            // set the owning side to null (unless already changed)
            if ($trainingAttendance->getTrainingAttendanceTraining() === $this) {
                $trainingAttendance->setTrainingAttendanceTraining(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTrainingSessions(): Collection
    {
        return $this->trainingSessions;
    }

    /**
     * @param TrainingSession $trainingSession
     * @return $this
     */
    public function addTrainingSessions(TrainingSession $trainingSession): self
    {
        if (!$this->trainingSessions->contains($trainingSession)) {
            $this->trainingSessions[] = $trainingSession;
            $trainingSession->setTrainingSessionTraining($this);
        }

        return $this;
    }

    /**
     * @param TrainingSession $trainingSession
     * @return $this
     */
    public function removeTrainingSessions(TrainingSession $trainingSession): self
    {
        if ($this->trainingSessions->contains($trainingSession)) {
            $this->trainingSessions->removeElement($trainingSession);
            // set the owning side to null (unless already changed)
            if ($trainingSession->getTrainingSessionTraining() === $this) {
                $trainingSession->setTrainingSessionTraining(null);
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
        return count($this->trainingSessions);
    }

    /**
     * @return int
     */
    public function getTrainingSessionsDurationTotal(): int
    {
        $total = 0;

        foreach ($this->trainingSessions as $session)
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
        return $this->trainingSessions[0]->getTrainingSessionDate();
    }

    /**
     * @return DateTime|null
     */
    public function getTrainingLastDate(): ?DateTime
    {
        return $this->trainingSessions[$this->getTrainingSessionsTotal()-1]->getTrainingSessionDate();
    }
}
