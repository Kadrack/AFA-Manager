<?php
// src/Entity/ClubClass.php
namespace App\Entity;

use App\Repository\ClubClassRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClubClass
 */
#[ORM\Table(name: 'clubClass')]
#[ORM\Entity(repositoryClass: ClubClassRepository::class)]
class ClubClass
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $clubClassId;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $clubClassDay;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'time')]
    private DateTime $clubClassStart;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'time')]
    private DateTime $clubClassEnd;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $clubClassType;

    /**
     * @var Club
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'clubClasses')]
    #[ORM\JoinColumn(name: 'clubClass_join_club', referencedColumnName: 'clubId')]
    private Club $clubClassClub;

    /**
     * @var ClubDojo
     */
    #[ORM\ManyToOne(targetEntity: ClubDojo::class, cascade: ['persist'], inversedBy: 'clubDojoClasses')]
    #[ORM\JoinColumn(name: 'clubClass_join_clubDojo', referencedColumnName: 'clubDojoId')]
    private ClubDojo $clubClassClubDojo;

    /**
     * @return int
     */
    public function getClubClassId(): int
    {
        return $this->clubClassId;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubClassId(int $set): self
    {
        $this->clubClassId = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getClubClassDay(bool $format = false): int|string
    {
        if ($format)
        {
            return $this->getClubClassDayText($this->clubClassDay);
        }
        else
        {
            return $this->clubClassDay;
        }
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getClubClassDayText(int $id = 0): array|string
    {
        $keys = array('Lundi' => 1, 'Mardi' => 2, 'Mercredi' => 3, 'Jeudi' => 4, 'Vendredi' => 5, 'Samedi' => 6, 'Dimanche' => 7);

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
    public function setClubClassDay(int $set): self
    {
        $this->clubClassDay = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string
     */
    public function getClubClassStart(bool $format = false): DateTime|string
    {
        return $format ? $this->clubClassStart->format('H') . 'h' . $this->clubClassStart->format('i') : $this->clubClassStart;
    }

    /**
     * @param DateTime $set
     *
     * @return $this
     */
    public function setClubClassStart(DateTime $set): self
    {
        $this->clubClassStart = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string
     */
    public function getClubClassEnd(bool $format = false): DateTime|string
    {
        return $format ? $this->clubClassEnd->format('H') . 'h' . $this->clubClassEnd->format('i') : $this->clubClassEnd;
    }

    /**
     * @param DateTime $set
     *
     * @return $this
     */
    public function setClubClassEnd(DateTime $set): self
    {
        $this->clubClassEnd = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getClubClassType(bool $format = false): int|string
    {
        return $format ? $this->getClubClassTypeText($this->clubClassType) : $this->clubClassType;
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getClubClassTypeText(int $id): array|string
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
    public function setClubClassType(int $set): self
    {
        $this->clubClassType = $set;

        return $this;
    }

    /**
     * @return Club
     */
    public function getClubClassClub(): Club
    {
        return $this->clubClassClub;
    }

    /**
     * @param Club $set
     *
     * @return $this
     */
    public function setClubClassClub(Club $set): self
    {
        $this->clubClassClub = $set;

        return $this;
    }

    /**
     * @return ClubDojo
     */
    public function getClubClassClubDojo(): ClubDojo
    {
        return $this->clubClassClubDojo;
    }

    /**
     * @return string|null
     */
    public function getClubClassDojoName(): string|null
    {
        return $this->clubClassClubDojo->getClubDojoName();
    }

    /**
     * @param ClubDojo $set
     *
     * @return $this
     */
    public function setClubClassClubDojo(ClubDojo $set): self
    {
        $this->clubClassClubDojo = $set;

        return $this;
    }
}
