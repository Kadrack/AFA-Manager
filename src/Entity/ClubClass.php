<?php
// src/Entity/ClubLesson.php
namespace App\Entity;

use App\Repository\ClubClassRepository;

use App\Service\ListData;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClubClass
 */
#[ORM\Table(name: 'club_class')]
#[ORM\Entity(repositoryClass: ClubClassRepository::class)]
class ClubClass
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $club_class_id;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $club_class_day;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTime $club_class_starting_hour;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'time', nullable: true)]
    private ?DateTime $club_class_ending_hour;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $club_class_type;

    /**
     * @var ClubDojo|null
     */
    #[ORM\ManyToOne(targetEntity: ClubDojo::class, cascade: ['persist'], inversedBy: 'club_dojo_classes')]
    #[ORM\JoinColumn(name: 'club_class_join_club_dojo', referencedColumnName: 'club_dojo_id', nullable: true)]
    private ?ClubDojo $club_class_dojo;

    /**
     * @var Club|null
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'club_classes')]
    #[ORM\JoinColumn(name: 'club_class_join_club', referencedColumnName: 'club_id', nullable: true)]
    private ?Club $club_class_club;

    /**
     * @return int
     */
    public function getClubClassId(): int
    {
        return $this->club_class_id;
    }

    /**
     * @param int $club_class_id
     * @return $this
     */
    public function setClubClassId(int $club_class_id): self
    {
        $this->club_class_id = $club_class_id;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubClassDay(): ?int
    {
        return $this->club_class_day;
    }

    /**
     * @param int|null $club_class_day
     * @return $this
     */
    public function setClubClassDay(?int $club_class_day): self
    {
        $this->club_class_day = $club_class_day;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClubClassStartingHour(): ?DateTime
    {
        return $this->club_class_starting_hour;
    }

    /**
     * @param DateTime|null $club_class_starting_hour
     * @return $this
     */
    public function setClubClassStartingHour(?DateTime $club_class_starting_hour): self
    {
        $this->club_class_starting_hour = $club_class_starting_hour;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClubClassEndingHour(): ?DateTime
    {
        return $this->club_class_ending_hour;
    }

    /**
     * @param DateTime|null $club_class_ending_hour
     * @return $this
     */
    public function setClubClassEndingHour(?DateTime $club_class_ending_hour): self
    {
        $this->club_class_ending_hour = $club_class_ending_hour;

        return $this;
    }

    /**
     * @return int
     */
    public function getClubClassType(): int
    {
        return $this->club_class_type;
    }

    /**
     * @param int $club_class_type
     * @return $this
     */
    public function setClubClassType(int $club_class_type): self
    {
        $this->club_class_type = $club_class_type;

        return $this;
    }

    /**
     * @return ClubDojo|null
     */
    public function getClubClassDojo(): ?ClubDojo
    {
        return $this->club_class_dojo;
    }

    /**
     * @param ClubDojo|null $club_class_dojo
     * @return $this
     */
    public function setClubClassDojo(?ClubDojo $club_class_dojo): self
    {
        $this->club_class_dojo = $club_class_dojo;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getClubClassClub(): ?Club
    {
        return $this->club_class_club;
    }

    /**
     * @param Club|null $club_class_club
     * @return $this
     */
    public function setClubClassClub(?Club $club_class_club): self
    {
        $this->club_class_club = $club_class_club;

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return string
     */
    public function getClubClassDayDisplay(): string
    {
        $listData = new ListData();

        return $listData->getDay($this->getClubClassDay());
    }

    /**
     * @return string
     */
    public function getClubClassTypeDisplay(): string
    {
        $listData = new ListData();

        return $listData->getClassType($this->getClubClassType());
    }

    /**
     * @return string|null
     */
    public function getClubClassDojoName(): ?string
    {
        return $this->getClubClassDojo()->getClubDojoName();
    }
}
