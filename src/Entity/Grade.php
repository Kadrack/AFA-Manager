<?php
// src/Entity/Grade.php
namespace App\Entity;

use App\Repository\GradeRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Grade
 */
#[ORM\Table(name: 'grade')]
#[ORM\Entity(repositoryClass: GradeRepository::class)]
class Grade
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $grade_id;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $grade_date;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $grade_rank;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $grade_status;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $grade_certificate;

    /**
     * @var Club|null
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'club_grades')]
    #[ORM\JoinColumn(name: 'grade_join_club', referencedColumnName: 'club_id', nullable: true)]
    private ?Club $grade_club;

    /**
     * @var GradeSessionCandidate|null
     */
    #[ORM\ManyToOne(targetEntity: GradeSessionCandidate::class, cascade: ['persist'], inversedBy: 'grade_session_candidate_grades')]
    #[ORM\JoinColumn(name: 'grade_join_grade_session_candidate', referencedColumnName: 'grade_session_candidate_id', nullable: true)]
    private ?GradeSessionCandidate $grade_session;

    /**
     * @var Member
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_grades')]
    #[ORM\JoinColumn(name: 'grade_join_member', referencedColumnName: 'member_id', nullable: false)]
    private Member $grade_member;

    /**
     * @return int
     */
    public function getGradeId(): int
    {
        return $this->grade_id;
    }

    /**
     * @param int $grade_id
     * @return $this
     */
    public function setGradeId(int $grade_id): self
    {
        $this->grade_id = $grade_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getGradeDate(): ?DateTime
    {
        return $this->grade_date;
    }

    /**
     * @param DateTime $grade_date
     * @return $this
     */
    public function setGradeDate(DateTime $grade_date): self
    {
        $this->grade_date = $grade_date;

        return $this;
    }

    /**
     * @return int
     */
    public function getGradeRank(): int
    {
        return $this->grade_rank;
    }

    /**
     * @param int $grade_rank
     * @return $this
     */
    public function setGradeRank(int $grade_rank): self
    {
        $this->grade_rank = $grade_rank;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeStatus(): ?int
    {
        return $this->grade_status;
    }

    /**
     * @param int|null $grade_status
     * @return $this
     */
    public function setGradeStatus(?int $grade_status): self
    {
        $this->grade_status = $grade_status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeCertificate(): ?string
    {
        return $this->grade_certificate;
    }

    /**
     * @param string|null $grade_certificate
     * @return $this
     */
    public function setGradeCertificate(?string $grade_certificate): self
    {
        $this->grade_certificate = $grade_certificate;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getGradeClub(): ?Club
    {
        return $this->grade_club;
    }

    /**
     * @param Club|null $grade_club
     * @return $this
     */
    public function setGradeClub(?Club $grade_club): self
    {
        $this->grade_club = $grade_club;

        return $this;
    }

    /**
     * @return GradeSessionCandidate|null
     */
    public function getGradeSession(): ?GradeSessionCandidate
    {
        return $this->grade_session;
    }

    /**
     * @param GradeSessionCandidate|null $grade_session
     * @return $this
     */
    public function setGradeSession(?GradeSessionCandidate $grade_session): self
    {
        $this->grade_session = $grade_session;

        return $this;
    }

    /**
     * @return Member
     */
    public function getGradeMember(): Member
    {
        return $this->grade_member;
    }

    /**
     * @param Member $grade_member
     * @return $this
     */
    public function setGradeMember(Member $grade_member): self
    {
        $this->grade_member = $grade_member;

        return $this;
    }
}
