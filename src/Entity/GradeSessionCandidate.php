<?php
// src/Entity/GradeSessionCandidate.php
namespace App\Entity;

use App\Repository\GradeSessionCandidateRepository;

use App\Service\ListData;
use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Grade
 */
#[ORM\Table(name: 'gradeSessionCandidate')]
#[ORM\Entity(repositoryClass: GradeSessionCandidateRepository::class)]
class GradeSessionCandidate
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $gradeSessionCandidateId;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $gradeSessionCandidateDate;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $gradeSessionCandidateRank;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $gradeSessionCandidatePaymentDate;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $gradeSessionCandidateComment;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $gradeSessionCandidateStaffComment;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $gradeSessionCandidateJury;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $gradeSessionCandidatePosition;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $gradeSessionCandidateResult;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $gradeSessionCandidateStatus;

    /**
     * @var GradeSession|null
     */
    #[ORM\ManyToOne(targetEntity: GradeSession::class, cascade: ['persist'], inversedBy: 'gradeSessionCandidates')]
    #[ORM\JoinColumn(name: 'gradeSessionCandidate_join_gradeSession', referencedColumnName: 'gradeSessionId', nullable: true)]
    private ?GradeSession $gradeSessionCandidateExam;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberGradeSessionCandidates')]
    #[ORM\JoinColumn(name: 'gradeSessionCandidate_join_member', referencedColumnName: 'memberId', nullable: false)]
    private ?Member $gradeSessionCandidateMember;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'gradeSession', targetEntity: Grade::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['gradeRank' => 'ASC'])]
    private ArrayCollection|Collection|null $gradeSessionCandidateGrades;

    /**
     * GradeSession constructor.
     */
    public function __construct()
    {
        $this->gradeSessionCandidateGrades = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getGradeSessionCandidateId(): int
    {
        return $this->gradeSessionCandidateId;
    }

    /**
     * @param int $gradeSessionCandidateId
     * @return $this
     */
    public function setGradeSessionCandidateId(int $gradeSessionCandidateId): self
    {
        $this->gradeSessionCandidateId = $gradeSessionCandidateId;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getGradeSessionCandidateDate(): ?DateTime
    {
        return $this->gradeSessionCandidateDate;
    }

    /**
     * @param DateTime $gradeSessionCandidateDate
     * @return $this
     */
    public function setGradeSessionCandidateDate(DateTime $gradeSessionCandidateDate): self
    {
        $this->gradeSessionCandidateDate = $gradeSessionCandidateDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionCandidateRank(): ?int
    {
        return $this->gradeSessionCandidateRank;
    }

    /**
     * @param int|null $gradeSessionCandidateRank
     * @return $this
     */
    public function setGradeSessionCandidateRank(?int $gradeSessionCandidateRank): self
    {
        $this->gradeSessionCandidateRank = $gradeSessionCandidateRank;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getGradeSessionCandidatePaymentDate(): ?DateTime
    {
        return $this->gradeSessionCandidatePaymentDate;
    }

    /**
     * @param DateTime $gradeSessionCandidatePaymentDate
     * @return $this
     */
    public function setGradeSessionCandidatePaymentDate(DateTime $gradeSessionCandidatePaymentDate): self
    {
        $this->gradeSessionCandidatePaymentDate = $gradeSessionCandidatePaymentDate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionCandidateComment(): ?string
    {
        return $this->gradeSessionCandidateComment;
    }

    /**
     * @param string|null $gradeSessionCandidateComment
     * @return $this
     */
    public function setGradeSessionCandidateComment(?string $gradeSessionCandidateComment): self
    {
        $this->gradeSessionCandidateComment = $gradeSessionCandidateComment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionCandidateStaffComment(): ?string
    {
        return $this->gradeSessionCandidateStaffComment;
    }

    /**
     * @param string|null $gradeSessionCandidateStaffComment
     * @return $this
     */
    public function setGradeSessionCandidateStaffComment(?string $gradeSessionCandidateStaffComment): self
    {
        $this->gradeSessionCandidateStaffComment = $gradeSessionCandidateStaffComment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionCandidateJury(): ?string
    {
        return $this->gradeSessionCandidateJury;
    }

    /**
     * @param string|null $gradeSessionCandidateJury
     * @return $this
     */
    public function setGradeSessionCandidateJury(?string $gradeSessionCandidateJury): self
    {
        $this->gradeSessionCandidateJury = $gradeSessionCandidateJury;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionCandidatePosition(): ?int
    {
        return $this->gradeSessionCandidatePosition;
    }

    /**
     * @param int|null $gradeSessionCandidatePosition
     * @return $this
     */
    public function setGradeSessionCandidatePosition(?int $gradeSessionCandidatePosition): self
    {
        $this->gradeSessionCandidatePosition = $gradeSessionCandidatePosition;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionCandidateResult(): ?int
    {
        return $this->gradeSessionCandidateResult;
    }

    /**
     * @param int|null $gradeSessionCandidateResult
     * @return $this
     */
    public function setGradeSessionCandidateResult(?int $gradeSessionCandidateResult): self
    {
        $this->gradeSessionCandidateResult = $gradeSessionCandidateResult;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionCandidateStatus(): ?int
    {
        return $this->gradeSessionCandidateStatus;
    }

    /**
     * @param int|null $gradeSessionCandidateStatus
     * @return $this
     */
    public function setGradeSessionCandidateStatus(?int $gradeSessionCandidateStatus): self
    {
        $this->gradeSessionCandidateStatus = $gradeSessionCandidateStatus;

        return $this;
    }

    /**
     * @return GradeSession|null
     */
    public function getGradeSessionCandidateExam(): ?GradeSession
    {
        return $this->gradeSessionCandidateExam;
    }

    /**
     * @param GradeSession|null $gradeSessionCandidateExam
     * @return $this
     */
    public function setGradeSessionCandidateExam(?GradeSession $gradeSessionCandidateExam): self
    {
        $this->gradeSessionCandidateExam = $gradeSessionCandidateExam;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getGradeSessionCandidateMember(): ?Member
    {
        return $this->gradeSessionCandidateMember;
    }

    /**
     * @param Member|null $gradeSessionCandidateMember
     * @return $this
     */
    public function setGradeSessionCandidateMember(?Member $gradeSessionCandidateMember): self
    {
        $this->gradeSessionCandidateMember = $gradeSessionCandidateMember;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGradeSessionCandidates(): Collection
    {
        return $this->gradeSessionCandidateGrades;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function addGradeSessionCandidates(Grade $grade): self
    {
        if (!$this->gradeSessionCandidateGrades->contains($grade)) {
            $this->gradeSessionCandidateGrades[] = $grade;
            $grade->setGradeSession($this);
        }

        return $this;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function removeGradeSessionGrades(Grade $grade): self
    {
        if ($this->gradeSessionCandidateGrades->contains($grade)) {
            $this->gradeSessionCandidateGrades->removeElement($grade);
            // set the owning side to null (unless already changed)
            if ($grade->getGradeSession() === $this) {
                $grade->setGradeSession(null);
            }
        }

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return int
     */
    public function getCandidateMemberId(): int
    {
        return $this->gradeSessionCandidateMember->getMemberId();
    }

    /**
     * @return string
     */
    public function getCandidateMemberFirstname(): string
    {
        return $this->gradeSessionCandidateMember->getMemberFirstname();
    }

    /**
     * @return string
     */
    public function getCandidateMemberName(): string
    {
        return $this->gradeSessionCandidateMember->getMemberName();
    }

    /**
     * @return string|null
     */
    public function getCandidateMemberEmail(): ?string
    {
        return $this->gradeSessionCandidateMember->getMemberEmail();
    }

    /**
     * @return Club
     */
    public function getCandidateMemberClub(): Club
    {
        return $this->gradeSessionCandidateMember->getMemberActualClub();
    }

    /**
     * @return string
     */
    public function getCandidateGrade(): string
    {
        $listData = new ListData();

        return $listData->getGrade($this->gradeSessionCandidateRank);
    }
}
