<?php
// src/Entity/TrainingSessionAttendance.php
namespace App\Entity;

use App\Repository\TrainingSessionAttendanceRepository;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class TrainingAttendance
 */
#[ORM\Table(name: 'training_session_attendance')]
#[ORM\Entity(repositoryClass: TrainingSessionAttendanceRepository::class)]
class TrainingSessionAttendance
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $training_session_attendance_id;

    /**
     * @var TrainingAttendance|null
     */
    #[ORM\ManyToOne(targetEntity: TrainingAttendance::class, cascade: ['persist'], inversedBy: 'training_attendance_sessions')]
    #[ORM\JoinColumn(name: 'training_session_attendance_join_training_attendance', referencedColumnName: 'training_attendance_id', nullable: true)]
    private ?TrainingAttendance $training_session_attendances;

    /**
     * @var TrainingSession|null
     */
    #[ORM\ManyToOne(targetEntity: TrainingSession::class, cascade: ['persist'], inversedBy: 'training_session_attendances')]
    #[ORM\JoinColumn(name: 'training_session_attendance_join_training_session', referencedColumnName: 'training_session_id', nullable: true)]
    private ?TrainingSession $training_session;

    /**
     * @return int
     */
    public function getTrainingSessionAttendanceId(): int
    {
        return $this->training_session_attendance_id;
    }

    /**
     * @param int $training_session_attendance_id
     * @return $this
     */
    public function setTrainingSessionAttendanceId(int $training_session_attendance_id): self
    {
        $this->training_session_attendance_id = $training_session_attendance_id;

        return $this;
    }

    /**
     * @return TrainingAttendance|null
     */
    public function getTrainingSessionAttendances(): ?TrainingAttendance
    {
        return $this->training_session_attendances;
    }

    /**
     * @param TrainingAttendance|null $training_session_attendances
     * @return $this
     */
    public function setTrainingSessionAttendances(?TrainingAttendance $training_session_attendances): self
    {
        $this->training_session_attendances = $training_session_attendances;

        return $this;
    }

    /**
     * @return TrainingSession|null
     */
    public function getTrainingSession(): ?TrainingSession
    {
        return $this->training_session;
    }

    /**
     * @param TrainingSession|null $training_session
     * @return $this
     */
    public function setTrainingSession(?TrainingSession $training_session): self
    {
        $this->training_session = $training_session;

        return $this;
    }
}
