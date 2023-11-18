<?php
// src/Entity/FormationSessionCandidate.php
namespace App\Entity;

use App\Repository\FormationSessionCandidateRepository;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Grade
 */
#[ORM\Table(name: 'formationSessionCandidate')]
#[ORM\Entity(repositoryClass: FormationSessionCandidateRepository::class)]
class FormationSessionCandidate
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $formationSessionCandidateId;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $formationSessionCandidateFirstname;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $formationSessionCandidateName;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $formationSessionCandidateSex;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $formationSessionCandidateAddress;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formationSessionCandidateZip;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formationSessionCandidateCity;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formationSessionCandidateCountry;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formationSessionCandidateEmail;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formationSessionCandidatePhone;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formationSessionCandidateBirthday;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $formationSessionCandidateGrade;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formationSessionCandidateClub;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formationSessionCandidateLicence;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formationSessionCandidateDate;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formationSessionCandidatePaymentDate;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $formationSessionCandidateResult;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $formationSessionCandidateStatus;

    /**
     * @var FormationSession|null
     */
    #[ORM\ManyToOne(targetEntity: FormationSession::class, cascade: ['persist'], inversedBy: 'formationSessionCandidates')]
    #[ORM\JoinColumn(name: 'formationSessionCandidate_join_formationSession', referencedColumnName: 'formationSessionId', nullable: true)]
    private ?FormationSession $formationSessionCandidateSession;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberFormationSessionCandidates')]
    #[ORM\JoinColumn(name: 'formationSessionCandidate_join_member', referencedColumnName: 'memberId', nullable: true)]
    private ?Member $formationSessionCandidateMember;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'formationSession', targetEntity: Formation::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['formationRank' => 'ASC'])]
    private ArrayCollection|Collection|null $formationSessionCandidateFormations;

    /**
     * FormtionSession constructor.
     */
    public function __construct()
    {
        $this->formationSessionCandidateFormations = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getFormationSessionCandidateId(): int
    {
        return $this->formationSessionCandidateId;
    }

    /**
     * @param int $formationSessionCandidateId
     * @return $this
     */
    public function setFormationSessionCandidateId(int $formationSessionCandidateId): self
    {
        $this->formationSessionCandidateId = $formationSessionCandidateId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormationSessionCandidateFirstname(): string
    {
        return ucwords(strtolower($this->formationSessionCandidateFirstname));
    }

    /**
     * @param string $formationSessionCandidateFirstname
     *
     * @return $this
     */
    public function setFormationSessionCandidateFirstname(string $formationSessionCandidateFirstname): self
    {
        $this->formationSessionCandidateFirstname = ucwords($formationSessionCandidateFirstname);

        return $this;
    }

    /**
     * @return string
     */
    public function getFormationSessionCandidateName(): string
    {
        return ucwords(strtolower($this->formationSessionCandidateName));
    }

    /**
     * @param string $formationSessionCandidateName
     *
     * @return $this
     */
    public function setFormationSessionCandidateName(string $formationSessionCandidateName): self
    {
        $this->formationSessionCandidateName = ucwords($formationSessionCandidateName);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationSessionCandidateSex(): ?int
    {
        return $this->formationSessionCandidateSex;
    }

    /**
     * @param int|null $formationSessionCandidateSex
     *
     * @return $this
     */
    public function setFormationSessionCandidateSex(?int $formationSessionCandidateSex): self
    {
        $this->formationSessionCandidateSex = $formationSessionCandidateSex;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateAddress(): ?string
    {
        return $this->formationSessionCandidateAddress;
    }

    /**
     * @param string|null $formationSessionCandidateAddress
     *
     * @return $this
     */
    public function setFormationSessionCandidateAddress(?string $formationSessionCandidateAddress): self
    {
        $this->formationSessionCandidateAddress = $formationSessionCandidateAddress;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateZip(): ?string
    {
        return $this->formationSessionCandidateZip;
    }

    /**
     * @param string $formationSessionCandidateZip
     *
     * @return $this
     */
    public function setFormationSessionCandidateZip(string $formationSessionCandidateZip): self
    {
        $this->formationSessionCandidateZip = $formationSessionCandidateZip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateCity(): ?string
    {
        return $this->formationSessionCandidateCity;
    }

    /**
     * @param string|null $formationSessionCandidateCity
     *
     * @return $this
     */
    public function setFormationSessionCandidateCity(?string $formationSessionCandidateCity): self
    {
        $this->formationSessionCandidateCity = $formationSessionCandidateCity;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateCountry(): ?string
    {
        return $this->formationSessionCandidateCountry;
    }

    /**
     * @param string|null $formationSessionCandidateCountry
     *
     * @return $this
     */
    public function setFormationSessionCandidateCountry(?string $formationSessionCandidateCountry): self
    {
        $this->formationSessionCandidateCountry = $formationSessionCandidateCountry;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateEmail(): ?string
    {
        return $this->formationSessionCandidateEmail;
    }

    /**
     * @param string|null $formationSessionCandidateEmail
     *
     * @return $this
     */
    public function setFormationSessionCandidateEmail(?string $formationSessionCandidateEmail): self
    {
        $this->formationSessionCandidateEmail = $formationSessionCandidateEmail;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidatePhone(): ?string
    {
        return $this->formationSessionCandidatePhone;
    }

    /**
     * @param string|null $formationSessionCandidatePhone
     *
     * @return $this
     */
    public function setFormationSessionCandidatePhone(?string $formationSessionCandidatePhone): self
    {
        $this->formationSessionCandidatePhone = $formationSessionCandidatePhone;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionCandidateBirthday(): ?DateTime
    {
        return $this->formationSessionCandidateBirthday;
    }

    /**
     * @param DateTime|null $formationSessionCandidateBirthday
     *
     * @return $this
     */
    public function setFormationSessionCandidateBirthday(?DateTime $formationSessionCandidateBirthday): self
    {
        $this->formationSessionCandidateBirthday = $formationSessionCandidateBirthday;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationSessionCandidateGrade(): ?int
    {
        return $this->formationSessionCandidateGrade;
    }

    /**
     * @param int|null $formationSessionCandidateGrade
     *
     * @return $this
     */
    public function setFormationSessionCandidateGrade(?int $formationSessionCandidateGrade): self
    {
        $this->formationSessionCandidateGrade = $formationSessionCandidateGrade;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateClub(): ?string
    {
        return $this->formationSessionCandidateClub;
    }

    /**
     * @param string|null $formationSessionCandidateClub
     *
     * @return $this
     */
    public function setFormationSessionCandidateClub(?string $formationSessionCandidateClub): self
    {
        $this->formationSessionCandidateClub = $formationSessionCandidateClub;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateLicence(): ?string
    {
        return $this->formationSessionCandidateLicence;
    }

    /**
     * @param string|null $formationSessionCandidateLicence
     *
     * @return $this
     */
    public function setFormationSessionCandidateLicence(?string $formationSessionCandidateLicence): self
    {
        $this->formationSessionCandidateLicence = $formationSessionCandidateLicence;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionCandidateDate(): ?DateTime
    {
        return $this->formationSessionCandidateDate;
    }

    /**
     * @param DateTime $formationSessionCandidateDate
     * @return $this
     */
    public function setFormationSessionCandidateDate(DateTime $formationSessionCandidateDate): self
    {
        $this->formationSessionCandidateDate = $formationSessionCandidateDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionCandidatePaymentDate(): ?DateTime
    {
        return $this->formationSessionCandidatePaymentDate;
    }

    /**
     * @param DateTime $formationSessionCandidatePaymentDate
     * @return $this
     */
    public function setFormationSessionCandidatePaymentDate(DateTime $formationSessionCandidatePaymentDate): self
    {
        $this->formationSessionCandidatePaymentDate = $formationSessionCandidatePaymentDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationSessionCandidateResult(): ?int
    {
        return $this->formationSessionCandidateResult;
    }

    /**
     * @param int|null $formationSessionCandidateResult
     * @return $this
     */
    public function setFormationSessionCandidateResult(?int $formationSessionCandidateResult): self
    {
        $this->formationSessionCandidateResult = $formationSessionCandidateResult;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationSessionCandidateStatus(): ?int
    {
        return $this->formationSessionCandidateStatus;
    }

    /**
     * @param int|null $formationSessionCandidateStatus
     * @return $this
     */
    public function setFormationSessionCandidateStatus(?int $formationSessionCandidateStatus): self
    {
        $this->formationSessionCandidateStatus = $formationSessionCandidateStatus;

        return $this;
    }

    /**
     * @return FormationSession|null
     */
    public function getFormationSessionCandidateSession(): ?FormationSession
    {
        return $this->formationSessionCandidateSession;
    }

    /**
     * @param FormationSession|null $formationSessionCandidateSession
     * @return $this
     */
    public function setFormationSessionCandidateSession(?FormationSession $formationSessionCandidateSession): self
    {
        $this->formationSessionCandidateSession = $formationSessionCandidateSession;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getFormationSessionCandidateMember(): ?Member
    {
        return $this->formationSessionCandidateMember;
    }

    /**
     * @param Member|null $formationSessionCandidateMember
     * @return $this
     */
    public function setFormationSessionCandidateMember(?Member $formationSessionCandidateMember): self
    {
        $this->formationSessionCandidateMember = $formationSessionCandidateMember;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFormationSessionCandidates(): Collection
    {
        return $this->formationSessionCandidateFormations;
    }

    /**
     * @param Formation $formation
     * @return $this
     */
    public function addFormationSessionCandidates(Formation $formation): self
    {
        if (!$this->formationSessionCandidateFormations->contains($formation)) {
            $this->formationSessionCandidateFormations[] = $formation;
            $formation->setFormationSession($this);
        }

        return $this;
    }

    /**
     * @param Formation $formation
     * @return $this
     */
    public function removeFormationSessionCandidates(Formation $formation): self
    {
        if ($this->formationSessionCandidateFormations->contains($formation)) {
            $this->formationSessionCandidateFormations->removeElement($formation);
            // set the owning side to null (unless already changed)
            if ($formation->getFormationSession() === $this) {
                $formation->setFormationSession(null);
            }
        }

        return $this;
    }
}
