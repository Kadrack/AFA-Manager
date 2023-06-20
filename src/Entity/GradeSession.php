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
#[ORM\Table(name: 'grade_session')]
#[ORM\Entity(repositoryClass: GradeSessionRepository::class)]
class GradeSession
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $grade_session_id;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private DateTime $grade_session_date;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private DateTime $grade_session_open;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private DateTime $grade_session_close;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $grade_session_type;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $grade_session_place;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $grade_session_street;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $grade_session_zip;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $grade_session_city;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'grade_session_candidate_exam', targetEntity: GradeSessionCandidate::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['grade_session_candidate_status' => 'ASC', 'grade_session_candidate_rank' => 'ASC', 'grade_session_candidate_jury' => 'ASC', 'grade_session_candidate_position' => 'ASC'])]
    private ArrayCollection|Collection|null $grade_session_candidates;

    /**
     * GradeSession constructor.
     */
    public function __construct()
    {
        $this->grade_session_candidates = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getGradeSessionId(): int
    {
        return $this->grade_session_id;
    }

    /**
     * @param int $grade_session_id
     * @return $this
     */
    public function setGradeSessionId(int $grade_session_id): self
    {
        $this->grade_session_id = $grade_session_id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getGradeSessionDate(): DateTime
    {
        return $this->grade_session_date;
    }

    /**
     * @param DateTime $grade_session_date
     * @return $this
     */
    public function setGradeSessionDate(DateTime $grade_session_date): self
    {
        $this->grade_session_date = $grade_session_date;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getGradeSessionOpen(): DateTime
    {
        return $this->grade_session_open;
    }

    /**
     * @param DateTime $grade_session_open
     * @return $this
     */
    public function setGradeSessionOpen(DateTime $grade_session_open): self
    {
        $this->grade_session_open = $grade_session_open;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getGradeSessionClose(): DateTime
    {
        return $this->grade_session_close;
    }

    /**
     * @param DateTime $grade_session_close
     * @return $this
     */
    public function setGradeSessionClose(DateTime $grade_session_close): self
    {
        $this->grade_session_close = $grade_session_close;

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
        return $this->grade_session_type;
    }

    /**
     * @param int $grade_session_type
     * @return $this
     */
    public function setGradeSessionType(int $grade_session_type): self
    {
        $this->grade_session_type = $grade_session_type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionPlace(): ?string
    {
        return $this->grade_session_place;
    }

    /**
     * @param string|null $grade_session_place
     * @return $this
     */
    public function setGradeSessionPlace(?string $grade_session_place): self
    {
        $this->grade_session_place = $grade_session_place;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionStreet(): ?string
    {
        return $this->grade_session_street;
    }

    /**
     * @param string|null $grade_session_street
     * @return $this
     */
    public function setGradeSessionStreet(?string $grade_session_street): self
    {
        $this->grade_session_street = $grade_session_street;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionZip(): ?int
    {
        return $this->grade_session_zip;
    }

    /**
     * @param int|null $grade_session_zip
     * @return $this
     */
    public function setGradeSessionZip(?int $grade_session_zip): self
    {
        $this->grade_session_zip = $grade_session_zip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionCity(): ?string
    {
        return $this->grade_session_city;
    }

    /**
     * @param string|null $grade_session_city
     * @return $this
     */
    public function setGradeSessionCity(?string $grade_session_city): self
    {
        $this->grade_session_city = $grade_session_city;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGradeSessionCandidates(): Collection
    {
        return $this->grade_session_candidates;
    }

    /**
     * @param GradeSessionCandidate $gradeSessionCandidate
     * @return $this
     */
    public function addGradeSessionCandidates(GradeSessionCandidate $gradeSessionCandidate): self
    {
        if (!$this->grade_session_candidates->contains($gradeSessionCandidate)) {
            $this->grade_session_candidates[] = $gradeSessionCandidate;
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
        if ($this->grade_session_candidates->contains($gradeSessionCandidate)) {
            $this->grade_session_candidates->removeElement($gradeSessionCandidate);
            // set the owning side to null (unless already changed)
            if ($gradeSessionCandidate->getGradeSessionCandidateExam() === $this) {
                $gradeSessionCandidate->setGradeSessionCandidateExam(null);
            }
        }

        return $this;
    }
}
