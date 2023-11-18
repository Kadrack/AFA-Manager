<?php
// src/Entity/ClubDojo.php
namespace App\Entity;

use App\Repository\ClubDojoRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClubDojo
 */
#[ORM\Table(name: 'clubDojo')]
#[ORM\Entity(repositoryClass: ClubDojoRepository::class)]
class ClubDojo
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $clubDojoId;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubDojoName = null;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $clubDojoStreet;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $clubDojoZip;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $clubDojoCity;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int|null $clubDojoTatamis = null;

    /**
     * @var bool|null
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool|null $clubDojoDea = null;

    /**
     * @var Club
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'clubDojos')]
    #[ORM\JoinColumn(name: 'clubDojo_join_club', referencedColumnName: 'clubId')]
    private Club $clubDojoClub;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubClassClubDojo', targetEntity: ClubClass::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection|null $clubDojoClasses;

    /**
     * ClubDojo constructor.
     */
    public function __construct()
    {
        $this->clubDojoClasses = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getClubDojoId(): int
    {
        return $this->clubDojoId;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubDojoId(int $set): self
    {
        $this->clubDojoId = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubDojoName(): string|null
    {
        return is_null($this->clubDojoName) ? 'Non défini' : $this->clubDojoName;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubDojoName(string|null $set = null): self
    {
        $this->clubDojoName = $set;

        return $this;
    }

    /**
     * @return string
     */
    public function getClubDojoStreet(): string
    {
        return $this->clubDojoStreet;
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setClubDojoStreet(string $set): self
    {
        $this->clubDojoStreet = $clubDojoStreet;

        return $this;
    }

    /**
     * @return int
     */
    public function getClubDojoZip(): int
    {
        return $this->clubDojoZip;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubDojoZip(int $set): self
    {
        $this->clubDojoZip = $set;

        return $this;
    }

    /**
     * @return string
     */
    public function getClubDojoCity(): string
    {
        return ucwords(strtolower($this->clubDojoCity));
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setClubDojoCity(string $set): self
    {
        $this->clubDojoCity = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string|null
     */
    public function getClubDojoTatamis(bool $format = false): int|string|null
    {
        if (is_null($this->clubDojoTatamis))
        {
            return $format ? 'Inconnu' : null;
        }

        return $format ? $this->clubDojoTatamis . 'm²' : $this->clubDojoTatamis;
    }

    /**
     * @param int|null $set
     *
     * @return $this
     */
    public function setClubDojoTatamis(int|null $set = null): self
    {
        $this->clubDojoTatamis = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getClubDojoDea(bool $format = false): string|null
    {
        $text = array(0 => 'Non', 1 => 'Oui');

        if (is_null($this->clubDojoDea))
        {
            return $format ? 'Inconnu' : null;
        }

        return $format ? $text[$this->clubDojoDea] : $this->clubDojoDea;
    }

    /**
     * @param bool|null $set
     *
     * @return $this
     */
    public function setClubDojoDea(bool|null $set = null): self
    {
        $this->clubDojoDea = $set;

        return $this;
    }

    /**
     * @return Club
     */
    public function getClubDojoClub(): Club
    {
        return $this->clubDojoClub;
    }

    /**
     * @param Club $set
     *
     * @return $this
     */
    public function setClubDojoClub(Club $set): self
    {
        $this->clubDojoClub = $set;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubDojoClasses(): Collection
    {
        return $this->clubDojoClasses;
    }
}
