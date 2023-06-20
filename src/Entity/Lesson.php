<?php
// src/Entity/Lesson.php
namespace App\Entity;

use App\Repository\LessonRepository;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Training
 */
#[ORM\Table(name: 'lesson')]
#[ORM\Entity(repositoryClass: LessonRepository::class)]
class Lesson
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $lesson_id;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $lesson_date;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'time')]
    private DateTime $lesson_starting_hour;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $lesson_duration;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $lesson_type;

    /**
     * @var Club
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'club_lessons')]
    #[ORM\JoinColumn(name: 'lesson_join_club', referencedColumnName: 'club_id')]
    private Club $lesson_club;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: LessonAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['lesson_attendance_id' => 'DESC'])]
    private ArrayCollection|Collection|null $lesson_attendances;

    /**
     * Lesson constructor.
     */
    public function __construct()
    {
        $this->lesson_attendances = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getLessonId(): int
    {
        return $this->lesson_id;
    }

    /**
     * @param int $lesson_id
     * @return $this
     */
    public function setLessonId(int $lesson_id): self
    {
        $this->lesson_id = $lesson_id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLessonDate(): DateTime
    {
        return $this->lesson_date;
    }

    /**
     * @param DateTime $lesson_date
     * @return $this
     */
    public function setLessonDate(DateTime $lesson_date): self
    {
        $this->lesson_date = $lesson_date;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getLessonStartingHour(): DateTime
    {
        return $this->lesson_starting_hour;
    }

    /**
     * @param DateTime $lesson_starting_hour
     * @return $this
     */
    public function setLessonStartingHour(DateTime $lesson_starting_hour): self
    {
        $this->lesson_starting_hour = $lesson_starting_hour;

        return $this;
    }

    /**
     * @return int
     */
    public function getLessonDuration(): int
    {
        return $this->lesson_duration;
    }

    /**
     * @param int $lesson_duration
     * @return $this
     */
    public function setLessonDuration(int $lesson_duration): self
    {
        $this->lesson_duration = $lesson_duration;

        return $this;
    }

    /**
     * @return int
     */
    public function getLessonType(): int
    {
        return $this->lesson_type;
    }

    /**
     * @param int $lesson_type
     * @return $this
     */
    public function setLessonType(int $lesson_type): self
    {
        $this->lesson_type = $lesson_type;

        return $this;
    }

    /**
     * @return Club
     */
    public function getLessonClub(): Club
    {
        return $this->lesson_club;
    }

    /**
     * @param Club $lesson_club
     * @return $this
     */
    public function setLessonClub(Club $lesson_club): self
    {
        $this->lesson_club = $lesson_club;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getLessonAttendances(): Collection
    {
        return $this->lesson_attendances;
    }

    /**
     * @param LessonAttendance $lesson_attendance
     * @return $this
     */
    public function addLessonAttendances(LessonAttendance $lesson_attendance): self
    {
        if (!$this->lesson_attendances->contains($lesson_attendance)) {
            $this->lesson_attendances[] = $lesson_attendance;
            $lesson_attendance->setLesson($this);
        }

        return $this;
    }

    /**
     * @param LessonAttendance $lesson_attendance
     * @return $this
     */
    public function removeLessonAttendances(LessonAttendance $lesson_attendance): self
    {
        if ($this->lesson_attendances->contains($lesson_attendance)) {
            $this->lesson_attendances->removeElement($lesson_attendance);
            // set the owning side to null (unless already changed)
            if ($lesson_attendance->getLesson() === $this) {
                $lesson_attendance->setLesson(null);
            }
        }

        return $this;
    }
}
