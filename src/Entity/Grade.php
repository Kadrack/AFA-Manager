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
    private int $gradeId;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $gradeDate;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $gradeRank;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $gradeStatus;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $gradeCertificate;

    /**
     * @var Club|null
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'grade_join_club', referencedColumnName: 'clubId', nullable: true)]
    private ?Club $gradeClub;

    /**
     * @var GradeSessionCandidate|null
     */
    #[ORM\ManyToOne(targetEntity: GradeSessionCandidate::class, cascade: ['persist'], inversedBy: 'gradeSessionCandidateGrades')]
    #[ORM\JoinColumn(name: 'grade_join_gradeSessionCandidate', referencedColumnName: 'gradeSessionCandidateId', nullable: true)]
    private ?GradeSessionCandidate $gradeSession;

    /**
     * @var Member
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberGrades')]
    #[ORM\JoinColumn(name: 'grade_join_member', referencedColumnName: 'memberId', nullable: false)]
    private Member $gradeMember;

    /**
     * @return int
     */
    public function getGradeId(): int
    {
        return $this->gradeId;
    }

    /**
     * @param int $gradeId
     *
     * @return $this
     */
    public function setGradeId(int $gradeId): self
    {
        $this->gradeId = $gradeId;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getGradeDate(bool $format = false): DateTime|string|null
    {
        if (is_null($this->gradeDate))
        {
            return $format ? 'En attente' : null;
        }

        return $format ? $this->gradeDate->format('d/m/Y') : $this->gradeDate;
    }

    /**
     * @param DateTime $gradeDate
     *
     * @return $this
     */
    public function setGradeDate(DateTime $gradeDate): self
    {
        $this->gradeDate = $gradeDate;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getGradeRank(bool $format = false): int|string
    {
        return $format ? $this->getGradeText($this->gradeRank) : $this->gradeRank;
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getGradeText(int $id = 0): array|string
    {
        $keys = array('6ème kyu' => 1, '5ème kyu' => 2, '4ème kyu' => 3, '3ème kyu' => 4, '2ème kyu' => 5, '1er kyu' => 6, '1er Dan National' => 7, '1er Dan Aïkikaï' => 8, '2ème Dan National' => 9, '2ème Dan Aïkikaï' => 10, '3ème Dan National' => 11, '3ème Dan Aïkikaï' => 12, '4ème Dan National' => 13, '4ème Dan Aïkikaï' => 14, '5ème Dan National' => 15, '5ème Dan Aïkikaï' => 16, '6ème Dan National' => 17, '6ème Dan Aïkikaï' => 18, '7ème Dan National' => 19, '7ème Dan Aïkikaï' => 20);

        if ($id == 0)
        {
            return $keys;
        }
        else if ($id > sizeof($keys))
        {
            return 'Autre';
        }

        return array_search($id, $keys);
    }

    /**
     * @param int $gradeRank
     *
     * @return $this
     */
    public function setGradeRank(int $gradeRank): self
    {
        $this->gradeRank = $gradeRank;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getGradeStatus(): ?int
    {
        return $this->gradeStatus;
    }

    /**
     * @param int|null $gradeStatus
     *
     * @return $this
     */
    public function setGradeStatus(?int $gradeStatus): self
    {
        $this->gradeStatus = $gradeStatus;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGradeCertificate(): ?string
    {
        return $this->gradeCertificate;
    }

    /**
     * @param string|null $gradeCertificate
     *
     * @return $this
     */
    public function setGradeCertificate(?string $gradeCertificate): self
    {
        $this->gradeCertificate = $gradeCertificate;

        return $this;
    }

    /**
     * @return Club
     */
    public function getGradeClub(): Club
    {
        return $this->gradeClub;
    }

    /**
     * @param Club $set
     *
     * @return $this
     */
    public function setGradeClub(Club $set): self
    {
        $this->gradeClub = $set;

        return $this;
    }

    /**
     * @return GradeSessionCandidate|null
     */
    public function getGradeSession(): ?GradeSessionCandidate
    {
        return $this->gradeSession;
    }

    /**
     * @param GradeSessionCandidate|null $gradeSession
     *
     * @return $this
     */
    public function setGradeSession(?GradeSessionCandidate $gradeSession): self
    {
        $this->gradeSession = $gradeSession;

        return $this;
    }

    /**
     * @return Member
     */
    public function getGradeMember(): Member
    {
        return $this->gradeMember;
    }

    /**
     * @param Member $gradeMember
     *
     * @return $this
     */
    public function setGradeMember(Member $gradeMember): self
    {
        $this->gradeMember = $gradeMember;

        return $this;
    }
}
