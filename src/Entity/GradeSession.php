<?php
// src/Entity/GradeSession.php
namespace App\Entity;

use App\Repository\GradeSessionRepository;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class GradeSession
 */
#[ORM\Table(name: 'gradeSession')]
#[ORM\Entity(repositoryClass: GradeSessionRepository::class)]
class GradeSession
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $gradeSessionId;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private DateTime $gradeSessionDate;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private DateTime $gradeSessionOpen;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private DateTime $gradeSessionClose;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $gradeSessionType;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $gradeSessionPlace = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $gradeSessionStreet = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int|null $gradeSessionZip = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $gradeSessionCity = null;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'gradeSessionCandidateExam', targetEntity: GradeSessionCandidate::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['gradeSessionCandidateStatus' => 'ASC', 'gradeSessionCandidateRank' => 'ASC', 'gradeSessionCandidateJury' => 'ASC', 'gradeSessionCandidatePosition' => 'ASC'])]
    private ArrayCollection|Collection|null $gradeSessionCandidates;

    /**
     * GradeSession constructor.
     */
    public function __construct()
    {
        $this->gradeSessionCandidates = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getGradeSessionId(): int
    {
        return $this->gradeSessionId;
    }

    /**
     * @param int $gradeSessionId
     *
     * @return $this
     */
    public function setGradeSessionId(int $gradeSessionId): self
    {
        $this->gradeSessionId = $gradeSessionId;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getGradeSessionDate(): DateTime
    {
        return $this->gradeSessionDate;
    }

    /**
     * @param DateTime $gradeSessionDate
     *
     * @return $this
     */
    public function setGradeSessionDate(DateTime $gradeSessionDate): self
    {
        $this->gradeSessionDate = $gradeSessionDate;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getGradeSessionOpen(): DateTime
    {
        return $this->gradeSessionOpen;
    }

    /**
     * @param DateTime $gradeSessionOpen
     *
     * @return $this
     */
    public function setGradeSessionOpen(DateTime $gradeSessionOpen): self
    {
        $this->gradeSessionOpen = $gradeSessionOpen;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getGradeSessionClose(): DateTime
    {
        return $this->gradeSessionClose;
    }

    /**
     * @param DateTime $gradeSessionClose
     *
     * @return $this
     */
    public function setGradeSessionClose(DateTime $gradeSessionClose): self
    {
        $this->gradeSessionClose = $gradeSessionClose;

        return $this;
    }

    /**
     * @return bool
     */
    public function getGradeSessionIsOpen(): bool
    {
        $today = new DateTime();

        if ($today >= $this->getGradeSessionOpen() && $today->setTime(0, 0) <= $this->getGradeSessionClose()->setTime(0, 0))
        {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getGradeSessionType(): int
    {
        return $this->gradeSessionType;
    }

    /**
     * @param int $gradeSessionType
     *
     * @return $this
     */
    public function setGradeSessionType(int $gradeSessionType): self
    {
        $this->gradeSessionType = $gradeSessionType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionPlace(): string|null
    {
        return $this->gradeSessionPlace;
    }

    /**
     * @param string|null $gradeSessionPlace
     *
     * @return $this
     */
    public function setGradeSessionPlace(string|null $gradeSessionPlace = null): self
    {
        $this->gradeSessionPlace = $gradeSessionPlace;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionStreet(): string|null
    {
        return $this->gradeSessionStreet;
    }

    /**
     * @param string|null $gradeSessionStreet
     *
     * @return $this
     */
    public function setGradeSessionStreet(string|null $gradeSessionStreet = null): self
    {
        $this->gradeSessionStreet = $gradeSessionStreet;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionZip(): int|null
    {
        return $this->gradeSessionZip;
    }

    /**
     * @param int|null $gradeSessionZip
     *
     * @return $this
     */
    public function setGradeSessionZip(int|null $gradeSessionZip = null): self
    {
        $this->gradeSessionZip = $gradeSessionZip;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getGradeSessionCity(bool $format = false): string|null
    {
        return $format ? $this->gradeSessionZip . ' ' . ucwords(strtolower($this->gradeSessionCity)) : ucwords(strtolower($this->gradeSessionCity));
    }

    /**
     * @param string|null $gradeSessionCity
     *
     * @return $this
     */
    public function setGradeSessionCity(string|null $gradeSessionCity = null): self
    {
        $this->gradeSessionCity = $gradeSessionCity;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGradeSessionCandidates(): Collection
    {
        return $this->gradeSessionCandidates;
    }

    /**
     * @param GradeSessionCandidate $gradeSessionCandidate
     * @return $this
     */
    public function addGradeSessionCandidates(GradeSessionCandidate $gradeSessionCandidate): self
    {
        if (!$this->gradeSessionCandidates->contains($gradeSessionCandidate)) {
            $this->gradeSessionCandidates[] = $gradeSessionCandidate;
            $gradeSessionCandidate->setGradeSessionCandidateExam($this);
        }

        return $this;
    }

    /**
     * @param GradeSessionCandidate $gradeSessionCandidate
     * @return $this
     */
    public function removeGradeSessionCandidates(GradeSessionCandidate $gradeSessionCandidate): self
    {
        if ($this->gradeSessionCandidates->contains($gradeSessionCandidate)) {
            $this->gradeSessionCandidates->removeElement($gradeSessionCandidate);
            // set the owning side to null (unless already changed)
            if ($gradeSessionCandidate->getGradeSessionCandidateExam() === $this) {
                $gradeSessionCandidate->setGradeSessionCandidateExam(null);
            }
        }

        return $this;
    }
}
