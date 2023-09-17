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
#[ORM\Table(name: 'grade_session_candidate')]
#[ORM\Entity(repositoryClass: GradeSessionCandidateRepository::class)]
class GradeSessionCandidate
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $grade_session_candidate_id;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $grade_session_candidate_date;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $grade_session_candidate_rank;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $grade_session_candidate_payment_date;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $grade_session_candidate_comment;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $grade_session_candidate_staff_comment;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $grade_session_candidate_jury;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $grade_session_candidate_position;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $grade_session_candidate_result;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $grade_session_candidate_status;

    /**
     * @var GradeSession|null
     */
    #[ORM\ManyToOne(targetEntity: GradeSession::class, cascade: ['persist'], inversedBy: 'grade_session_candidates')]
    #[ORM\JoinColumn(name: 'grade_session_candidate_join_grade_session', referencedColumnName: 'grade_session_id', nullable: true)]
    private ?GradeSession $grade_session_candidate_exam;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_exams')]
    #[ORM\JoinColumn(name: 'grade_session_candidate_join_member', referencedColumnName: 'member_id', nullable: false)]
    private ?Member $grade_session_candidate_member;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'grade_session', targetEntity: Grade::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['grade_rank' => 'ASC'])]
    private ArrayCollection|Collection|null $grade_session_candidate_grades;

    /**
     * GradeSession constructor.
     */
    public function __construct()
    {
        $this->grade_session_candidate_grades = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getGradeSessionCandidateId(): int
    {
        return $this->grade_session_candidate_id;
    }

    /**
     * @param int $gradeSessionCandidateId
     * @return $this
     */
    public function setGradeSessionCandidateId(int $gradeSessionCandidateId): self
    {
        $this->grade_session_candidate_id = $gradeSessionCandidateId;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getGradeSessionCandidateDate(): ?DateTime
    {
        return $this->grade_session_candidate_date;
    }

    /**
     * @param DateTime $gradeSessionCandidateDate
     * @return $this
     */
    public function setGradeSessionCandidateDate(DateTime $gradeSessionCandidateDate): self
    {
        $this->grade_session_candidate_date = $gradeSessionCandidateDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionCandidateRank(): ?int
    {
        return $this->grade_session_candidate_rank;
    }

    /**
     * @param int|null $gradeSessionCandidateRank
     * @return $this
     */
    public function setGradeSessionCandidateRank(?int $gradeSessionCandidateRank): self
    {
        $this->grade_session_candidate_rank = $gradeSessionCandidateRank;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getGradeSessionCandidatePaymentDate(): ?DateTime
    {
        return $this->grade_session_candidate_payment_date;
    }

    /**
     * @param DateTime $gradeSessionCandidatePaymentDate
     * @return $this
     */
    public function setGradeSessionCandidatePaymentDate(DateTime $gradeSessionCandidatePaymentDate): self
    {
        $this->grade_session_candidate_payment_date = $gradeSessionCandidatePaymentDate;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionCandidateComment(): ?string
    {
        return $this->grade_session_candidate_comment;
    }

    /**
     * @param string|null $gradeSessionCandidateComment
     * @return $this
     */
    public function setGradeSessionCandidateComment(?string $gradeSessionCandidateComment): self
    {
        $this->grade_session_candidate_comment = $gradeSessionCandidateComment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionCandidateStaffComment(): ?string
    {
        return $this->grade_session_candidate_staff_comment;
    }

    /**
     * @param string|null $gradeSessionCandidateStaffComment
     * @return $this
     */
    public function setGradeSessionCandidateStaffComment(?string $gradeSessionCandidateStaffComment): self
    {
        $this->grade_session_candidate_staff_comment = $gradeSessionCandidateStaffComment;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeSessionCandidateJury(): ?string
    {
        return $this->grade_session_candidate_jury;
    }

    /**
     * @param string|null $gradeSessionCandidateJury
     * @return $this
     */
    public function setGradeSessionCandidateJury(?string $gradeSessionCandidateJury): self
    {
        $this->grade_session_candidate_jury = $gradeSessionCandidateJury;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionCandidatePosition(): ?int
    {
        return $this->grade_session_candidate_position;
    }

    /**
     * @param int|null $gradeSessionCandidatePosition
     * @return $this
     */
    public function setGradeSessionCandidatePosition(?int $gradeSessionCandidatePosition): self
    {
        $this->grade_session_candidate_position = $gradeSessionCandidatePosition;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionCandidateResult(): ?int
    {
        return $this->grade_session_candidate_result;
    }

    /**
     * @param int|null $gradeSessionCandidateResult
     * @return $this
     */
    public function setGradeSessionCandidateResult(?int $gradeSessionCandidateResult): self
    {
        $this->grade_session_candidate_result = $gradeSessionCandidateResult;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeSessionCandidateStatus(): ?int
    {
        return $this->grade_session_candidate_status;
    }

    /**
     * @param int|null $gradeSessionCandidateStatus
     * @return $this
     */
    public function setGradeSessionCandidateStatus(?int $gradeSessionCandidateStatus): self
    {
        $this->grade_session_candidate_status = $gradeSessionCandidateStatus;

        return $this;
    }

    /**
     * @return GradeSession|null
     */
    public function getGradeSessionCandidateExam(): ?GradeSession
    {
        return $this->grade_session_candidate_exam;
    }

    /**
     * @param GradeSession|null $gradeSessionCandidateExam
     * @return $this
     */
    public function setGradeSessionCandidateExam(?GradeSession $gradeSessionCandidateExam): self
    {
        $this->grade_session_candidate_exam = $gradeSessionCandidateExam;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getGradeSessionCandidateMember(): ?Member
    {
        return $this->grade_session_candidate_member;
    }

    /**
     * @param Member|null $gradeSessionCandidateMember
     * @return $this
     */
    public function setGradeSessionCandidateMember(?Member $gradeSessionCandidateMember): self
    {
        $this->grade_session_candidate_member = $gradeSessionCandidateMember;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getGradeSessionCandidates(): Collection
    {
        return $this->grade_session_candidate_grades;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function addGradeSessionCandidates(Grade $grade): self
    {
        if (!$this->grade_session_candidate_grades->contains($grade)) {
            $this->grade_session_candidate_grades[] = $grade;
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
        if ($this->grade_session_candidate_grades->contains($grade)) {
            $this->grade_session_candidate_grades->removeElement($grade);
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
        return $this->grade_session_candidate_member->getMemberId();
    }

    /**
     * @return string
     */
    public function getCandidateMemberFirstname(): string
    {
        return $this->grade_session_candidate_member->getMemberFirstname();
    }

    /**
     * @return string
     */
    public function getCandidateMemberName(): string
    {
        return $this->grade_session_candidate_member->getMemberName();
    }

    /**
     * @return string|null
     */
    public function getCandidateMemberEmail(): ?string
    {
        return $this->grade_session_candidate_member->getMemberEmail();
    }

    /**
     * @return Club
     */
    public function getCandidateMemberClub(): Club
    {
        return $this->grade_session_candidate_member->getMemberActualClub();
    }

    /**
     * @return string
     */
    public function getCandidateGrade(): string
    {
        $listData = new ListData();

        return $listData->getGrade($this->grade_session_candidate_rank);
    }
}
