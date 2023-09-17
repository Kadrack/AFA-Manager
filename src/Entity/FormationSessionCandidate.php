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
#[ORM\Table(name: 'formation_session_candidate')]
#[ORM\Entity(repositoryClass: FormationSessionCandidateRepository::class)]
class FormationSessionCandidate
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $formation_session_candidate_id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $formation_session_candidate_firstname;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    private string $formation_session_candidate_name;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $formation_session_candidate_sex;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $formation_session_candidate_address;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formation_session_candidate_zip;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formation_session_candidate_city;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formation_session_candidate_country;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formation_session_candidate_email;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formation_session_candidate_phone;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formation_session_candidate_birthday;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formation_session_candidate_date;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formation_session_candidate_payment_date;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $formation_session_candidate_result;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $formation_session_candidate_status;

    /**
     * @var FormationSession|null
     */
    #[ORM\ManyToOne(targetEntity: FormationSession::class, cascade: ['persist'], inversedBy: 'formation_session_candidates')]
    #[ORM\JoinColumn(name: 'formation_session_candidate_join_formation_session', referencedColumnName: 'formation_session_id', nullable: true)]
    private ?FormationSession $formation_session_candidate_session;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_candidate_formations')]
    #[ORM\JoinColumn(name: 'formation_session_candidate_join_member', referencedColumnName: 'member_id', nullable: true)]
    private ?Member $formation_session_candidate_member;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'formation_session', targetEntity: Formation::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['formation_rank' => 'ASC'])]
    private ArrayCollection|Collection|null $formation_session_candidate_formations;

    /**
     * FormtionSession constructor.
     */
    public function __construct()
    {
        $this->formation_session_candidate_formations = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getFormationSessionCandidateId(): int
    {
        return $this->formation_session_candidate_id;
    }

    /**
     * @param int $formationSessionCandidateId
     * @return $this
     */
    public function setFormationSessionCandidateId(int $formationSessionCandidateId): self
    {
        $this->formation_session_candidate_id = $formationSessionCandidateId;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormationSessionCandidateFirstname(): string
    {
        return ucwords(strtolower($this->formation_session_candidate_firstname));
    }

    /**
     * @param string $formation_session_candidate_firstname
     * @return $this
     */
    public function setFormationSessionCandidateFirstname(string $formation_session_candidate_firstname): self
    {
        $this->formation_session_candidate_firstname = ucwords($formation_session_candidate_firstname);

        return $this;
    }

    /**
     * @return string
     */
    public function getFormationSessionCandidateName(): string
    {
        return ucwords(strtolower($this->formation_session_candidate_name));
    }

    /**
     * @param string $formation_session_candidate_name
     * @return $this
     */
    public function setFormationSessionCandidateName(string $formation_session_candidate_name): self
    {
        $this->formation_session_candidate_name = ucwords($formation_session_candidate_name);

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationSessionCandidateSex(): ?int
    {
        return $this->formation_session_candidate_sex;
    }

    /**
     * @param int|null $formation_session_candidate_sex
     * @return $this
     */
    public function setFormationSessionCandidateSex(?int $formation_session_candidate_sex): self
    {
        $this->formation_session_candidate_sex = $formation_session_candidate_sex;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateAddress(): ?string
    {
        return $this->formation_session_candidate_address;
    }

    /**
     * @param string|null $formation_session_candidate_address
     * @return $this
     */
    public function setFormationSessionCandidateAddress(?string $formation_session_candidate_address): self
    {
        $this->formation_session_candidate_address = $formation_session_candidate_address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateZip(): ?string
    {
        return $this->formation_session_candidate_zip;
    }

    /**
     * @param string $formation_session_candidate_zip
     * @return $this
     */
    public function setFormationSessionCandidateZip(string $formation_session_candidate_zip): self
    {
        $this->formation_session_candidate_zip = $formation_session_candidate_zip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateCity(): ?string
    {
        return $this->formation_session_candidate_city;
    }

    /**
     * @param string|null $formation_session_candidate_city
     * @return $this
     */
    public function setFormationSessionCandidateCity(?string $formation_session_candidate_city): self
    {
        $this->formation_session_candidate_city = $formation_session_candidate_city;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateCountry(): ?string
    {
        return $this->formation_session_candidate_country;
    }

    /**
     * @param string|null $formation_session_candidate_country
     * @return $this
     */
    public function setFormationSessionCandidateCountry(?string $formation_session_candidate_country): self
    {
        $this->formation_session_candidate_country = $formation_session_candidate_country;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateEmail(): ?string
    {
        return $this->formation_session_candidate_email;
    }

    /**
     * @param string|null $formation_session_candidate_email
     * @return $this
     */
    public function setFormationSessionCandidateEmail(?string $formation_session_candidate_email): self
    {
        $this->formation_session_candidate_email = $formation_session_candidate_email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidatePhone(): ?string
    {
        return $this->formation_session_candidate_phone;
    }

    /**
     * @param string|null $formation_session_candidate_phone
     * @return $this
     */
    public function setFormationSessionCandidatePhone(?string $formation_session_candidate_phone): self
    {
        $this->formation_session_candidate_phone = $formation_session_candidate_phone;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionCandidateBirthday(): ?DateTime
    {
        return $this->formation_session_candidate_birthday;
    }

    /**
     * @param DateTime|null $formation_session_candidate_birthday
     * @return $this
     */
    public function setFormationSessionCandidateBirthday(?DateTime $formation_session_candidate_birthday): self
    {
        $this->formation_session_candidate_birthday = $formation_session_candidate_birthday;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionCandidateDate(): ?DateTime
    {
        return $this->formation_session_candidate_date;
    }

    /**
     * @param DateTime $formationSessionCandidateDate
     * @return $this
     */
    public function setFormationSessionCandidateDate(DateTime $formationSessionCandidateDate): self
    {
        $this->formation_session_candidate_date = $formationSessionCandidateDate;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionCandidatePaymentDate(): ?DateTime
    {
        return $this->formation_session_candidate_payment_date;
    }

    /**
     * @param DateTime $formationSessionCandidatePaymentDate
     * @return $this
     */
    public function setFormationSessionCandidatePaymentDate(DateTime $formationSessionCandidatePaymentDate): self
    {
        $this->formation_session_candidate_payment_date = $formationSessionCandidatePaymentDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationSessionCandidateResult(): ?int
    {
        return $this->formation_session_candidate_result;
    }

    /**
     * @param int|null $formationSessionCandidateResult
     * @return $this
     */
    public function setFormationSessionCandidateResult(?int $formationSessionCandidateResult): self
    {
        $this->formation_session_candidate_result = $formationSessionCandidateResult;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationSessionCandidateStatus(): ?int
    {
        return $this->formation_session_candidate_status;
    }

    /**
     * @param int|null $formationSessionCandidateStatus
     * @return $this
     */
    public function setFormationSessionCandidateStatus(?int $formationSessionCandidateStatus): self
    {
        $this->formation_session_candidate_status = $formationSessionCandidateStatus;

        return $this;
    }

    /**
     * @return FormationSession|null
     */
    public function getFormationSessionCandidateSession(): ?FormationSession
    {
        return $this->formation_session_candidate_session;
    }

    /**
     * @param FormationSession|null $formationSessionCandidateSession
     * @return $this
     */
    public function setFormationSessionCandidateSession(?FormationSession $formationSessionCandidateSession): self
    {
        $this->formation_session_candidate_session = $formationSessionCandidateSession;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getFormationSessionCandidateMember(): ?Member
    {
        return $this->formation_session_candidate_member;
    }

    /**
     * @param Member|null $formationSessionCandidateMember
     * @return $this
     */
    public function setFormationSessionCandidateMember(?Member $formationSessionCandidateMember): self
    {
        $this->formation_session_candidate_member = $formationSessionCandidateMember;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFormationSessionCandidates(): Collection
    {
        return $this->formation_session_candidate_formations;
    }

    /**
     * @param Formation $formation
     * @return $this
     */
    public function addFormationSessionCandidates(Formation $formation): self
    {
        if (!$this->formation_session_candidate_formations->contains($formation)) {
            $this->formation_session_candidate_formations[] = $formation;
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
        if ($this->formation_session_candidate_formations->contains($formation)) {
            $this->formation_session_candidate_formations->removeElement($formation);
            // set the owning side to null (unless already changed)
            if ($formation->getFormationSession() === $this) {
                $formation->setFormationSession(null);
            }
        }

        return $this;
    }
}
