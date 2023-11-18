<?php
// src/Entity/ClubLesson.php
namespace App\Entity;

use App\Repository\ClubLessonRepository;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClubLesson
 */
#[ORM\Table(name: 'clubLesson')]
#[ORM\Entity(repositoryClass: ClubLessonRepository::class)]
class ClubLesson
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $clubLessonId;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $clubLessonDate;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'time')]
    private DateTime $clubLessonStart;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $clubLessonDuration;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $clubLessonType;

    /**
     * @var Club
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'clubLessons')]
    #[ORM\JoinColumn(name: 'clubLesson_join_club', referencedColumnName: 'clubId')]
    private Club $clubLessonClub;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubLessonAttendanceClubLesson', targetEntity: ClubLessonAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['clubLessonAttendanceId' => 'DESC'])]
    private ArrayCollection|Collection|null $clubLessonAttendances;

    /**
     * Lesson constructor.
     */
    public function __construct()
    {
        $this->clubLessonAttendances = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getClubLessonId(): int
    {
        return $this->clubLessonId;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubLessonId(int $set): self
    {
        $this->clubLessonId = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string
     */
    public function getClubLessonDate(bool $format = false): DateTime|string
    {
        return $format ? $this->clubLessonDate->format('d/m/Y') : $this->clubLessonDate;
    }

    /**
     * @param DateTime $set
     *
     * @return $this
     */
    public function setClubLessonDate(DateTime $set): self
    {
        $this->clubLessonDate = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string
     */
    public function getClubLessonStart(bool $format = false): DateTime|string
    {
        return $format ? $this->clubLessonStart->format('H') . 'h' . $this->clubLessonStart->format('i') : $this->clubLessonStart;
    }

    /**
     * @param DateTime $set
     *
     * @return $this
     */
    public function setClubLessonStart(DateTime $set): self
    {
        $this->clubLessonStart = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getClubLessonDuration(bool $format = false): int|string
    {
        return $format ? floor($this->clubLessonDuration / 60) . "h" . $this->clubLessonDuration % 60 : $this->clubLessonDuration;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubLessonDuration(int $set): self
    {
        $this->clubLessonDuration = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getClubLessonType(bool $format = false): int|string
    {
        return $format ? $this->getClubLessonTypeText($this->clubLessonType) : $this->clubLessonType;
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getClubLessonTypeText(int $id): array|string
    {
        $keys = array('Cours Adultes' => 1, 'Cours Enfants' => 2, 'Cours Adultes/Enfants' => 3);

        if ($id == 0)
        {
            return $keys;
        }
        elseif ($id > sizeof($keys))
        {
            return "Autre";
        }
        else
        {
            return array_search($id, $keys);
        }
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubLessonType(int $set): self
    {
        $this->clubLessonType = $set;

        return $this;
    }

    /**
     * @return Club
     */
    public function getClubLessonClub(): Club
    {
        return $this->clubLessonClub;
    }

    /**
     * @param Club $clubLessonClub
     *
     * @return $this
     */
    public function setClubLessonClub(Club $clubLessonClub): self
    {
        $this->clubLessonClub = $clubLessonClub;

        return $this;
    }

    /**
     * @return Collection|int
     */
    public function getClubLessonAttendances(): Collection|int
    {
        return $this->clubLessonAttendances;
    }

    /**
     * @return int
     */
    public function getClubLessonAttendancesCount(): int
    {
        return sizeof($this->clubLessonAttendances);
    }
}
