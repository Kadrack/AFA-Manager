<?php
// src/Entity/ClubLessonAttendance.php
namespace App\Entity;

use App\Repository\ClubLessonAttendanceRepository;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClubLessonAttendance
 */
#[ORM\Table(name: 'clubLessonAttendance')]
#[ORM\Entity(repositoryClass: ClubLessonAttendanceRepository::class)]
class ClubLessonAttendance
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $clubLessonAttendanceId;

    /**
     * @var ClubLesson
     */
    #[ORM\ManyToOne(targetEntity: ClubLesson::class, cascade: ['persist'], inversedBy: 'clubLessonAttendances')]
    #[ORM\JoinColumn(name: 'clubLessonAttendance_join_clubLesson', referencedColumnName: 'clubLessonId')]
    private ClubLesson $clubLessonAttendanceClubLesson;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberClubLessonAttendances')]
    #[ORM\JoinColumn(name: 'clubLessonAttendance_join_member', referencedColumnName: 'memberId', nullable: true)]
    private Member|null $clubLessonAttendanceMember;

    /**
     * @return int
     */
    public function getClubLessonAttendanceId(): int
    {
        return $this->clubLessonAttendanceId;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubLessonAttendanceId(int $set): self
    {
        $this->clubLessonAttendanceId = $set;

        return $this;
    }

    /**
     * @return ClubLesson
     */
    public function getClubLessonAttendanceClubLesson(): ClubLesson
    {
        return $this->clubLessonAttendanceClubLesson;
    }

    /**
     * @param ClubLesson $set
     *
     * @return $this
     */
    public function setClubLessonAttendanceClubLesson(ClubLesson $set): self
    {
        $this->clubLessonAttendanceClubLesson = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return Member|string|null
     */
    public function getClubLessonAttendanceMember(bool $format = false): Member|string|null
    {
        if (is_null($this->clubLessonAttendanceMember))
        {
            return $format ? 'Invité' : null;
        }

        return $this->clubLessonAttendanceMember;
    }

    /**
     * @param Member|null $set
     *
     * @return $this
     */
    public function setClubLessonAttendanceMember(Member|null $set = null): self
    {
        $this->clubLessonAttendanceMember = $set;

        return $this;
    }
}
