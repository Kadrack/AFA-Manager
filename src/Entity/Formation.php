<?php
// src/Entity/Formation.php
namespace App\Entity;

use App\Repository\FormationRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Formation
 */
#[ORM\Table(name: 'formation')]
#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $formation_id;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formation_date;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $formation_rank;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formation_certificate;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $formation_status;

    /**
     * @var Member
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_formations')]
    #[ORM\JoinColumn(name: 'formation_join_member', referencedColumnName: 'member_id', nullable: false)]
    private Member $formation_member;

    /**
     * @var FormationSessionCandidate|null
     */
    #[ORM\ManyToOne(targetEntity: FormationSessionCandidate::class, cascade: ['persist'], inversedBy: 'formation_session_candidate_formations')]
    #[ORM\JoinColumn(name: 'formation_join_formation_session_candidate', referencedColumnName: 'formation_session_candidate_id', nullable: true)]
    private ?FormationSessionCandidate $formation_session;

    /**
     * @return int
     */
    public function getFormationId(): int
    {
        return $this->formation_id;
    }

    /**
     * @param int $formation_id
     * @return $this
     */
    public function setFormationId(int $formation_id): self
    {
        $this->formation_id = $formation_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationDate(): ?DateTime
    {
        return $this->formation_date;
    }

    /**
     * @param DateTime $formation_date
     * @return $this
     */
    public function setFormationDate(DateTime $formation_date): self
    {
        $this->formation_date = $formation_date;

        return $this;
    }

    /**
     * @return int
     */
    public function getFormationRank(): int
    {
        return $this->formation_rank;
    }

    /**
     * @param int $formation_rank
     * @return $this
     */
    public function setFormationRank(int $formation_rank): self
    {
        $this->formation_rank = $formation_rank;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationCertificate(): ?string
    {
        return $this->formation_certificate;
    }

    /**
     * @param string|null $formation_certificate
     * @return $this
     */
    public function setFormationCertificate(?string $formation_certificate): self
    {
        $this->formation_certificate = $formation_certificate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationStatus(): ?int
    {
        return $this->formation_status;
    }

    /**
     * @param int|null $formation_status
     * @return $this
     */
    public function setFormationStatus(?int $formation_status): self
    {
        $this->formation_status = $formation_status;

        return $this;
    }

    /**
     * @return FormationSessionCandidate|null
     */
    public function getFormationSession(): ?GradeSessionCandidate
    {
        return $this->formation_session;
    }

    /**
     * @param FormationSessionCandidate|null $formation_session
     * @return $this
     */
    public function setFormationSession(?FormationSessionCandidate $formation_session): self
    {
        $this->formation_session = $formation_session;

        return $this;
    }

    /**
     * @return Member
     */
    public function getFormationMember(): Member
    {
        return $this->formation_member;
    }

    /**
     * @param Member $formation_member
     * @return $this
     */
    public function setFormationMember(Member $formation_member): self
    {
        $this->formation_member = $formation_member;

        return $this;
    }
}
