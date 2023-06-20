<?php
// src/Entity/LessonAttendance.php
namespace App\Entity;

use App\Repository\LessonAttendanceRepository;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class LessonAttendance
 */
#[ORM\Table(name: 'lesson_attendance')]
#[ORM\Entity(repositoryClass: LessonAttendanceRepository::class)]
class LessonAttendance
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $lesson_attendance_id;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lesson_attendance_name;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $lesson_attendance_sex;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $lesson_attendance_country;

    /**
     * @var Lesson|null
     */
    #[ORM\ManyToOne(targetEntity: Lesson::class, cascade: ['persist'], inversedBy: 'lesson_attendances')]
    #[ORM\JoinColumn(name: 'lesson_attendance_join_lesson', referencedColumnName: 'lesson_id', nullable: false)]
    private ?Lesson $lesson;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_lesson_attendances')]
    #[ORM\JoinColumn(name: 'lesson_attendance_join_member', referencedColumnName: 'member_id', nullable: true)]
    private ?Member $lesson_attendance_member;

    /**
     * @return int
     */
    public function getLessonAttendanceId(): int
    {
        return $this->lesson_attendance_id;
    }

    /**
     * @param int $lesson_attendance_id
     * @return $this
     */
    public function setLessonAttendanceId(int $lesson_attendance_id): self
    {
        $this->lesson_attendance_id = $lesson_attendance_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLessonAttendanceName(): ?string
    {
        return $this->lesson_attendance_name;
    }

    /**
     * @param string|null $lesson_attendance_name
     * @return $this
     */
    public function setLessonAttendanceName(?string $lesson_attendance_name): self
    {
        $this->lesson_attendance_name = $lesson_attendance_name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLessonAttendanceSex(): ?int
    {
        return $this->lesson_attendance_sex;
    }

    /**
     * @param int|null $lesson_attendance_sex
     * @return $this
     */
    public function setLessonAttendanceSex(?int $lesson_attendance_sex): self
    {
        $this->lesson_attendance_sex = $lesson_attendance_sex;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLessonAttendanceCountry(): ?string
    {
        return $this->lesson_attendance_country;
    }

    /**
     * @param string|null $lesson_attendance_country
     * @return $this
     */
    public function setLessonAttendanceCountry(?string $lesson_attendance_country): self
    {
        $this->lesson_attendance_country = $lesson_attendance_country;

        return $this;
    }

    /**
     * @return Lesson|null
     */
    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    /**
     * @param Lesson|null $lesson
     * @return $this
     */
    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getLessonAttendanceMember(): ?Member
    {
        return $this->lesson_attendance_member;
    }

    /**
     * @param Member|null $member
     * @return $this
     */
    public function setLessonAttendanceMember(?Member $member): self
    {
        $this->lesson_attendance_member = $member;

        return $this;
    }
}
