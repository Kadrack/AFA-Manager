<?php
// src/Entity/TrainingSessionAttendance.php
namespace App\Entity;

use App\Repository\TrainingSessionAttendanceRepository;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TrainingAttendance
 */
#[ORM\Table(name: 'trainingSessionAttendance')]
#[ORM\Entity(repositoryClass: TrainingSessionAttendanceRepository::class)]
class TrainingSessionAttendance
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $trainingSessionAttendanceId;

    /**
     * @var TrainingAttendance
     */
    #[ORM\ManyToOne(targetEntity: TrainingAttendance::class, cascade: ['persist'], inversedBy: 'trainingAttendanceSessions')]
    #[ORM\JoinColumn(name: 'trainingSessionAttendance_join_trainingAttendance', referencedColumnName: 'trainingAttendanceId')]
    private TrainingAttendance $trainingSessionAttendanceTrainingAttendance;

    /**
     * @var TrainingSession
     */
    #[ORM\ManyToOne(targetEntity: TrainingSession::class, cascade: ['persist'], inversedBy: 'trainingSessionAttendances')]
    #[ORM\JoinColumn(name: 'trainingSessionAttendance_join_trainingSession', referencedColumnName: 'trainingSessionId')]
    private TrainingSession $trainingSessionAttendanceTrainingSession;

    /**
     * @return int
     */
    public function getTrainingSessionAttendanceId(): int
    {
        return $this->trainingSessionAttendanceId;
    }

    /**
     * @param int $trainingSessionAttendanceId
     *
     * @return $this
     */
    public function setTrainingSessionAttendanceId(int $trainingSessionAttendanceId): self
    {
        $this->trainingSessionAttendanceId = $trainingSessionAttendanceId;

        return $this;
    }

    /**
     * @return TrainingAttendance
     */
    public function getTrainingSessionAttendanceTrainingAttendance(): TrainingAttendance
    {
        return $this->trainingSessionAttendanceTrainingAttendance;
    }

    /**
     * @param TrainingAttendance $set
     *
     * @return $this
     */
    public function setTrainingSessionAttendanceTrainingAttendance(TrainingAttendance $set): self
    {
        $this->trainingSessionAttendanceTrainingAttendance = $set;

        return $this;
    }

    /**
     * @return TrainingSession
     */
    public function getTrainingSessionAttendanceTrainingSession(): TrainingSession
    {
        return $this->trainingSessionAttendanceTrainingSession;
    }

    /**
     * @param TrainingSession $set
     *
     * @return $this
     */
    public function setTrainingSessionAttendanceTrainingSession(TrainingSession $set): self
    {
        $this->trainingSessionAttendanceTrainingSession = $set;

        return $this;
    }
}
