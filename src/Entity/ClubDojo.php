<?php
// src/Entity/ClubDojo.php
namespace App\Entity;

use App\Repository\ClubDojoRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ClubDojo
 */
#[ORM\Table(name: 'club_dojo')]
#[ORM\Entity(repositoryClass: ClubDojoRepository::class)]
class ClubDojo
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $club_dojo_id;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_dojo_name;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $club_dojo_street;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank]
    private ?int $club_dojo_zip;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $club_dojo_city;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $club_dojo_tatamis;

    /**
     * @var bool|null
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $club_dojo_dea;

    /**
     * @var Club|null
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'club_dojos')]
    #[ORM\JoinColumn(name: 'club_dojo_join_club', referencedColumnName: 'club_id', nullable: true)]
    private ?Club $club_dojo_club;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'club_class_dojo', targetEntity: ClubClass::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection|null $club_dojo_classes;

    /**
     * ClubDojo constructor.
     */
    public function __construct()
    {
        $this->club_dojo_classes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getClubDojoId(): int
    {
        return $this->club_dojo_id;
    }

    /**
     * @param int $club_dojo_id
     * @return $this
     */
    public function setClubDojoId(int $club_dojo_id): self
    {
        $this->club_dojo_id = $club_dojo_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubDojoName(): ?string
    {
        return $this->club_dojo_name;
    }

    /**
     * @param string|null $club_dojo_name
     * @return $this
     */
    public function setClubDojoName(?string $club_dojo_name): self
    {
        $this->club_dojo_name = $club_dojo_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubDojoStreet(): ?string
    {
        return $this->club_dojo_street;
    }

    /**
     * @param string|null $club_dojo_street
     * @return $this
     */
    public function setClubDojoStreet(?string $club_dojo_street): self
    {
        $this->club_dojo_street = $club_dojo_street;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubDojoZip(): ?int
    {
        return $this->club_dojo_zip;
    }

    /**
     * @param int|null $club_dojo_zip
     * @return $this
     */
    public function setClubDojoZip(?int $club_dojo_zip): self
    {
        $this->club_dojo_zip = $club_dojo_zip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubDojoCity(): ?string
    {
        return $this->club_dojo_city;
    }

    /**
     * @param string|null $club_dojo_city
     * @return $this
     */
    public function setClubDojoCity(?string $club_dojo_city): self
    {
        $this->club_dojo_city = $club_dojo_city;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubDojoTatamis(): ?int
    {
        return $this->club_dojo_tatamis;
    }

    /**
     * @param int|null $club_dojo_tatamis
     * @return $this
     */
    public function setClubDojoTatamis(?int $club_dojo_tatamis): self
    {
        $this->club_dojo_tatamis = $club_dojo_tatamis;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getClubDojoDEA(): ?bool
    {
        return $this->club_dojo_dea;
    }

    /**
     * @param bool|null $club_dojo_dea
     * @return $this
     */
    public function setClubDojoDEA(?bool $club_dojo_dea): self
    {
        $this->club_dojo_dea = $club_dojo_dea;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getClubDojoClub(): ?Club
    {
        return $this->club_dojo_club;
    }

    /**
     * @param Club|null $club_dojo_club
     * @return $this
     */
    public function setClubDojoClub(?Club $club_dojo_club): self
    {
        $this->club_dojo_club = $club_dojo_club;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubDojoClasses(): Collection
    {
        return $this->club_dojo_classes;
    }

    /**
     * @param ClubClass $club_dojo_classes
     * @return $this
     */
    public function addClubDojoClasses(ClubClass $club_dojo_classes): self
    {
        if (!$this->club_dojo_classes->contains($club_dojo_classes)) {
            $this->club_dojo_classes[] = $club_dojo_classes;
            $club_dojo_classes->setClubClassDojo($this);
        }

        return $this;
    }

    /**
     * @param ClubClass $club_dojo_classes
     * @return $this
     */
    public function removeClubDojoClasses(ClubClass $club_dojo_classes): self
    {
        if ($this->club_dojo_classes->contains($club_dojo_classes)) {
            $this->club_dojo_classes->removeElement($club_dojo_classes);
            // set the owning side to null (unless already changed)
            if ($club_dojo_classes->getClubClassDojo() === $this) {
                $club_dojo_classes->setClubClassDojo(null);
            }
        }

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return string
     */
    public function getClubDojoDEADisplay(): string
    {
        if ($this->getClubDojoDEA())
        {
            return 'Oui';
        }

        return 'Non';
    }
}
