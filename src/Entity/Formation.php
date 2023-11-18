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
    private int $formationId;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formationDate;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $formationRank;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formationCertificate;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $formationStatus;

    /**
     * @var Member
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberFormations')]
    #[ORM\JoinColumn(name: 'formation_join_member', referencedColumnName: 'memberId', nullable: false)]
    private Member $formationMember;

    /**
     * @var FormationSessionCandidate|null
     */
    #[ORM\ManyToOne(targetEntity: FormationSessionCandidate::class, cascade: ['persist'], inversedBy: 'formationSessionCandidateFormations')]
    #[ORM\JoinColumn(name: 'formation_join_formationSessionCandidate', referencedColumnName: 'formationSessionCandidateId', nullable: true)]
    private ?FormationSessionCandidate $formationSession;

    /**
     * @return int
     */
    public function getFormationId(): int
    {
        return $this->formationId;
    }

    /**
     * @param int $formationId
     *
     * @return $this
     */
    public function setFormationId(int $formationId): self
    {
        $this->formationId = $formationId;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationDate(): ?DateTime
    {
        return $this->formationDate;
    }

    /**
     * @param DateTime $formationDate
     *
     * @return $this
     */
    public function setFormationDate(DateTime $formationDate): self
    {
        $this->formationDate = $formationDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getFormationRank(): int
    {
        return $this->formationRank;
    }

    /**
     * @param int $formationRank
     *
     * @return $this
     */
    public function setFormationRank(int $formationRank): self
    {
        $this->formationRank = $formationRank;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationCertificate(): ?string
    {
        return $this->formationCertificate;
    }

    /**
     * @param string|null $formationCertificate
     *
     * @return $this
     */
    public function setFormationCertificate(?string $formationCertificate): self
    {
        $this->formationCertificate = $formationCertificate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationStatus(): ?int
    {
        return $this->formationStatus;
    }

    /**
     * @param int|null $formationStatus
     *
     * @return $this
     */
    public function setFormationStatus(?int $formationStatus): self
    {
        $this->formationStatus = $formationStatus;

        return $this;
    }

    /**
     * @return FormationSessionCandidate|null
     */
    public function getFormationSession(): ?GradeSessionCandidate
    {
        return $this->formationSession;
    }

    /**
     * @param FormationSessionCandidate|null $formationSession
     *
     * @return $this
     */
    public function setFormationSession(?FormationSessionCandidate $formationSession): self
    {
        $this->formationSession = $formationSession;

        return $this;
    }

    /**
     * @return Member
     */
    public function getFormationMember(): Member
    {
        return $this->formationMember;
    }

    /**
     * @param Member $formationMember
     *
     * @return $this
     */
    public function setFormationMember(Member $formationMember): self
    {
        $this->formationMember = $formationMember;

        return $this;
    }
}
