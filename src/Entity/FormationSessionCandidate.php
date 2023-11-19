<?php
// src/Entity/FormationSessionCandidate.php
namespace App\Entity;

use App\Repository\FormationSessionCandidateRepository;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Intl\Countries;
use Symfony\Component\Mime\Address;

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
    private int|null $formationSessionCandidateSex;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private string|null $formationSessionCandidateAddress;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $formationSessionCandidateZip;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $formationSessionCandidateCity;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $formationSessionCandidateCountry;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $formationSessionCandidateEmail;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $formationSessionCandidatePhone;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private DateTime|null $formationSessionCandidateBirthday;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int|null $formationSessionCandidateGrade;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $formationSessionCandidateClub;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $formationSessionCandidateLicence;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private DateTime|null $formationSessionCandidateDate;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private DateTime|null $formationSessionCandidatePaymentDate;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int|null $formationSessionCandidateResult;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int|null $formationSessionCandidateStatus;

    /**
     * @var FormationSession|null
     */
    #[ORM\ManyToOne(targetEntity: FormationSession::class, cascade: ['persist'], inversedBy: 'formationSessionCandidates')]
    #[ORM\JoinColumn(name: 'formationSessionCandidate_join_formationSession', referencedColumnName: 'formationSessionId', nullable: true)]
    private FormationSession|null $formationSessionCandidateSession;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberFormationSessionCandidates')]
    #[ORM\JoinColumn(name: 'formationSessionCandidate_join_member', referencedColumnName: 'memberId', nullable: true)]
    private Member|null $formationSessionCandidateMember;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'formationSession', targetEntity: Formation::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['formationRank' => 'ASC'])]
    private ArrayCollection|Collection|null $formationSessionCandidateFormations;

    /**
     * FormationSessionCandidate constructor.
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
     * @param int $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateId(int $set): self
    {
        $this->formationSessionCandidateId = $set;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormationSessionCandidateFirstname(): string
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            return ucwords(strtolower($this->formationSessionCandidateFirstname));
        }

        return $this->formationSessionCandidateMember->getMemberFirstname();
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateFirstname(string $set): self
    {
        $this->formationSessionCandidateFirstname = ucwords($set);

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string
     */
    public function getFormationSessionCandidateName(bool $format = false): string
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            return $format ? $this->getFormationSessionCandidateFirstname() . ' ' . $this->getFormationSessionCandidateName() : ucwords(strtolower($this->formationSessionCandidateName));
        }

        return $this->formationSessionCandidateMember->getMemberName($format);
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateName(string $set): self
    {
        $this->formationSessionCandidateName = ucwords($set);

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string|null
     */
    public function getFormationSessionCandidateSex(bool $format = false): int|string|null
    {
        if (is_null($this->formationSessionCandidateMember) && is_null($this->formationSessionCandidateSex))
        {
            return $format ? 'Non défini' : null;
        }
        elseif (is_null($this->formationSessionCandidateMember))
        {
            return $format ? $this->getFormationSessionCandidateSexText($this->formationSessionCandidateSex) : $this->formationSessionCandidateSex;
        }

        return $this->formationSessionCandidateMember->getMemberSex($format);
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getFormationSessionCandidateSexText(int $id = 0): array|string
    {
        $keys = array('Masculin' => 1, 'Féminin' => 2, 'Non défini' => 3);

        if ($id == 0)
        {
            return $keys;
        }
        elseif ($id > sizeof($keys))
        {
            return "Autre";
        }

        return array_search($id, $keys);
    }

    /**
     * @param int|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateSex(int|null $set = null): self
    {
        $this->formationSessionCandidateSex = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateAddress(): string|null
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            return $this->formationSessionCandidateAddress;
        }

        return $this->formationSessionCandidateMember->getMemberAddress();
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateAddress(string|null $set = null): self
    {
        $this->formationSessionCandidateAddress = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateZip(): string|null
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            return $this->formationSessionCandidateZip;
        }

        return $this->formationSessionCandidateMember->getMemberZip();
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateZip(string|null $set = null): self
    {
        $this->formationSessionCandidateZip = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getFormationSessionCandidateCity(bool $format = false): string|null
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            if (is_null($this->formationSessionCandidateCity))
            {
                return $format ? 'Non disponible' : null;
            }

            return $format ? $this->getFormationSessionCandidateZip() . ' ' . $this->getFormationSessionCandidateCity() : ucwords(strtolower($this->formationSessionCandidateCity));
        }

        return $this->formationSessionCandidateMember->getMemberCity($format);
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateCity(string|null $set = null): self
    {
        $this->formationSessionCandidateCity = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormationSessionCandidateCountry(): string|null
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            return is_null($this->formationSessionCandidateCountry) ? 'Non disponible' : Countries::getName($this->formationSessionCandidateCountry);
        }

        return $this->formationSessionCandidateMember->getMemberCountry();
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateCountry(string|null $set = null): self
    {
        $this->formationSessionCandidateCountry = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return Address|string|null
     */
    public function getFormationSessionCandidateEmail(bool $format = false): Address|string|null
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            if (is_null($this->formationSessionCandidateEmail))
            {
                return $format ? null : 'Non disponible';
            }

            return $format ? new Address($this->formationSessionCandidateEmail, $this->getFormationSessionCandidateFirstname() . ' ' . $this->getFormationSessionCandidateName()) : $this->formationSessionCandidateEmail;
        }

        return $this->formationSessionCandidateMember->getMemberEmail($format);
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateEmail(string|null $set = null): self
    {
        $this->formationSessionCandidateEmail = $set;

        return $this;
    }

    /**
     * @param bool|null $format
     *
     * @return string|null
     */
    public function getFormationSessionCandidatePhone(bool $format = false): string|null
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            if (is_null($this->formationSessionCandidatePhone))
            {
                return $format ? 'Non disponible' : null;
            }

            return $this->formationSessionCandidatePhone;
        }

        return $this->formationSessionCandidateMember->getMemberPhone($format);
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidatePhone(string|null $set = null): self
    {
        $this->formationSessionCandidatePhone = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getFormationSessionCandidateBirthday(bool $format = false): DateTime|string|null
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            if (is_null($this->formationSessionCandidateBirthday))
            {
                return $format ? 'Non disponible' : null;
            }

            return $format ? $this->formationSessionCandidateBirthday->format('d/m/Y') : $this->formationSessionCandidateBirthday;
        }

        return $this->formationSessionCandidateMember->getMemberBirthday($format);
    }

    /**
     * @param DateTime|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateBirthday(DateTime|null $set = null): self
    {
        $this->formationSessionCandidateBirthday = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string|null
     */
    public function getFormationSessionCandidateGrade(bool $format = false): int|string|null
    {
        if (is_null($this->formationSessionCandidateMember) && is_null($this->formationSessionCandidateGrade))
        {
            return $format ? 'Non défini' : null;
        }
        elseif (is_null($this->formationSessionCandidateMember))
        {
            return $format ? $this->getFormationSessionCandidateGradeText($this->formationSessionCandidateGrade) : $this->formationSessionCandidateGrade;
        }

        return $this->formationSessionCandidateMember->getMemberLastGrade($format);
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getFormationSessionCandidateGradeText(int $id = 0): array|string
    {
        $keys = array('2ème Kyu'  => 1, '1er yu'  => 2, '1er Dan' => 3, '2ème Dan' => 4, '3ème Dan' => 5, '4ème Dan' => 6, '5ème Dan' => 7, '6ème Dan' => 8, '7ème Dan' => 9);

        if ($id == 0)
        {
            return $keys;
        }
        elseif ($id > sizeof($keys))
        {
            return "Autre";
        }

        return array_search($id, $keys);
    }

    /**
     * @param int|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateGrade(int|null $set = null): self
    {
        $this->formationSessionCandidateGrade = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getFormationSessionCandidateClub(bool $format = false): string|null
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            if (is_null($this->formationSessionCandidateClub))
            {
                return $format ? 'Non disponible' : null;
            }

            return $this->formationSessionCandidateClub;
        }

        return $this->formationSessionCandidateMember->getMemberActualClub($format);
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateClub(string|null $set = null): self
    {
        $this->formationSessionCandidateClub = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|int|null
     */
    public function getFormationSessionCandidateLicence(bool $format = false): string|int|null
    {
        if (is_null($this->formationSessionCandidateMember))
        {
            if (is_null($this->formationSessionCandidateLicence))
            {
                return $format ? 'Non disponible' : null;
            }

            return $this->formationSessionCandidateLicence;
        }

        return $this->formationSessionCandidateMember->getMemberId();
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateLicence(string|null $set = null): self
    {
        $this->formationSessionCandidateLicence = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getFormationSessionCandidateDate(bool $format = false): DateTime|string|null
    {
        if (is_null($this->formationSessionCandidateDate))
        {
            return $format ? 'Non disponible' : null;
        }

        return $format ? $this->formationSessionCandidateDate->format('d/m/Y') : $this->formationSessionCandidateDate;
    }

    /**
     * @param DateTime|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateDate(DateTime|null $set = null): self
    {
        $this->formationSessionCandidateDate = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getFormationSessionCandidatePaymentDate(bool $format = false): DateTime|string|null
    {
        if (is_null($this->formationSessionCandidatePaymentDate))
        {
            return $format ? 'En attente' : null;
        }

        return $format ? $this->formationSessionCandidatePaymentDate->format('d/m/Y') : $this->formationSessionCandidatePaymentDate;
    }

    /**
     * @param DateTime|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidatePaymentDate(DateTime|null $set = null): self
    {
        $this->formationSessionCandidatePaymentDate = $set;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationSessionCandidateResult(): int|null
    {
        return $this->formationSessionCandidateResult;
    }

    /**
     * @param int|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateResult(int|null $set = null): self
    {
        $this->formationSessionCandidateResult = $set;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getFormationSessionCandidateStatus(): int|null
    {
        return $this->formationSessionCandidateStatus;
    }

    /**
     * @param int|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateStatus(int|null $set = null): self
    {
        $this->formationSessionCandidateStatus = $set;

        return $this;
    }

    /**
     * @return FormationSession|null
     */
    public function getFormationSessionCandidateSession(): FormationSession|null
    {
        return $this->formationSessionCandidateSession;
    }

    /**
     * @param FormationSession|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateSession(FormationSession|null $set = null): self
    {
        $this->formationSessionCandidateSession = $set;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getFormationSessionCandidateMember(): Member|null
    {
        return $this->formationSessionCandidateMember;
    }

    /**
     * @param Member|null $set
     *
     * @return $this
     */
    public function setFormationSessionCandidateMember(Member|null $set = null): self
    {
        $this->formationSessionCandidateMember = $set;

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
     * @param Formation $set
     *
     * @return $this
     */
    public function addFormationSessionCandidates(Formation $set): self
    {
        if (!$this->formationSessionCandidateFormations->contains($set)) {
            $this->formationSessionCandidateFormations[] = $set;
            $set->setFormationSession($this);
        }

        return $this;
    }

    /**
     * @param Formation $set
     *
     * @return $this
     */
    public function removeFormationSessionCandidates(Formation $set): self
    {
        if ($this->formationSessionCandidateFormations->contains($set)) {
            $this->formationSessionCandidateFormations->removeElement($set);
            // set the owning side to null (unless already changed)
            if ($set->getFormationSession() === $this) {
                $set->setFormationSession(null);
            }
        }

        return $this;
    }
}
