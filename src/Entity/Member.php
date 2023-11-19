<?php
// src/Entity/Member.php
namespace App\Entity;

use App\Repository\MemberRepository;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Intl\Countries;

use Symfony\Component\Mime\Address;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Member
 */
#[ORM\Table(name: 'member')]
#[ORM\Entity(repositoryClass: MemberRepository::class)]
class Member
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $memberId;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $memberFirstname;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $memberName;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private string|null $memberPhoto = null;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $memberSex;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank]
    private string|null $memberAddress = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private string|null $memberZip = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private string|null $memberCity = null;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $memberCountry;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Email]
    private string|null $memberEmail = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $memberPhone = null;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $memberBirthday;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $memberStartPractice;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $memberAikikaiId = null;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private int $memberSubscriptionStatus = 1;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private DateTime|null $memberSubscriptionValidity;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer', options: ['default' => 1])]
    private int $memberSubscriptionList = 1;

    /**
     * @var int
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private int $memberLastKagami = 0;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubLessonAttendanceMember', targetEntity: ClubLessonAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $memberClubLessonAttendances;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubManagerMember', targetEntity: ClubManager::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $memberClubManagers;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubTeacherMember', targetEntity: ClubTeacher::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $memberClubTeachers;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'clusterMember', targetEntity: ClusterMember::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $memberClusterMembers;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'formationMember', targetEntity: Formation::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['formationDate' => 'DESC'])]
    private ArrayCollection|Collection|null $memberFormations;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'formationSessionCandidateMember', targetEntity: FormationSessionCandidate::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $memberFormationSessionCandidates;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'gradeMember', targetEntity: Grade::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['gradeRank' => 'DESC', 'gradeSession' => 'DESC'])]
    private ArrayCollection|Collection|null $memberGrades;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'gradeSessionCandidateMember', targetEntity: GradeSessionCandidate::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['gradeSessionCandidateRank' => 'DESC'])]
    private ArrayCollection|Collection|null $memberGradeSessionCandidates;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'memberLicenceMember', targetEntity: MemberLicence::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['memberLicenceId' => 'DESC'])]
    private ArrayCollection|Collection|null $memberLicences;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'titleMember', targetEntity: Title::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['titleDate' => 'DESC'])]
    private ArrayCollection|Collection|null $memberTitles;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'trainingAttendanceMember', targetEntity: TrainingAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $memberTrainingAttendances;

    /**
     * @var User|null
     */
    #[ORM\OneToOne(mappedBy: 'member', targetEntity: User::class, cascade: ['persist'], orphanRemoval: true)]
    private User|null $memberUser;

    /**
     * Member constructor.
     */
    public function __construct()
    {
        $this->memberClubLessonAttendances      = new ArrayCollection();
        $this->memberClubManagers               = new ArrayCollection();
        $this->memberClubTeachers               = new ArrayCollection();
        $this->memberClusterMembers             = new ArrayCollection();
        $this->memberFormationSessionCandidates = new ArrayCollection();
        $this->memberGrades                     = new ArrayCollection();
        $this->memberGradeSessionCandidates     = new ArrayCollection();
        $this->memberFormations                 = new ArrayCollection();
        $this->memberLicences                   = new ArrayCollection();
        $this->memberTitles                     = new ArrayCollection();
        $this->memberTrainingAttendances        = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getMemberId(): int
    {
        return $this->memberId;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setMemberId(int $set): self
    {
        $this->memberId = $set;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberFirstname(): string
    {
        return ucwords(strtolower($this->memberFirstname));
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setMemberFirstname(string $set): self
    {
        $this->memberFirstname = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string
     */
    public function getMemberName(bool $format = false): string
    {
        return $format ? $this->getMemberFirstname() . ' ' . $this->getMemberName() : ucwords(strtolower($this->memberName));
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setMemberName(string $set): self
    {
        $this->memberName = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberPhoto(): string|null
    {
        return $this->memberPhoto;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setMemberPhoto(string|null $set = null): self
    {
        $this->memberPhoto = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getMemberSex(bool $format = false): int|string
    {
        return $format ? $this->getMemberSexText($this->memberSex) : $this->memberSex;
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getMemberSexText(int $id = 0): array|string
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
     * @param int $set
     *
     * @return $this
     */
    public function setMemberSex(int $set): self
    {
        $this->memberSex = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberAddress(): string|null
    {
        return $this->memberAddress;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setMemberAddress(string|null $set = null): self
    {
        $this->memberAddress = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberZip(): string|null
    {
        return $this->memberZip;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setMemberZip(string|null $set = null): self
    {
        $this->memberZip = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getMemberCity(bool $format = false): string|null
    {
        if (is_null($this->memberCity))
        {
            return $format ? 'Non disponible' : null;
        }

        return $format ? $this->getMemberZip() . ' ' . $this->getMemberCity() : ucwords(strtolower($this->memberCity));
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setMemberCity(string|null $set = null): self
    {
        $this->memberCity = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberCountry(): string
    {
        return Countries::getName($this->memberCountry);
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setMemberCountry(string $set): self
    {
        $this->memberCountry = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return Address|string|null
     */
    public function getMemberEmail(bool $format = false): Address|string|null
    {
        if (is_null($this->memberEmail))
        {
            return $format ? 'Non disponible' : null;
        }

        return $format ? new Address($this->memberEmail, $this->getMemberFirstname() . ' ' . $this->getMemberName()) : $this->memberEmail;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setMemberEmail(string|null $set = null): self
    {
        $this->memberEmail = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getMemberPhone(bool $format = false): string|null
    {
        if (is_null($this->memberPhone))
        {
            return $format ? 'Non disponible' : null;
        }

        return $this->memberPhone;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setMemberPhone(string|null $set = null): self
    {
        $this->memberPhone = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string
     */
    public function getMemberBirthday(bool $format = false): DateTime|string
    {
        return $format ? $this->memberBirthday->format('d/m/Y') : $this->memberBirthday;
    }

    /**
     * @return bool
     */
    public function getMemberIsChild(): bool
    {
        if ($this->getMemberBirthday() >= new DateTime('-14 year today'))
        {
            return true;
        }

        return false;
    }

    /**
     * @param bool $format
     *
     * @return int
     */
    public function getMemberAge(bool $format = false): int
    {
        $age = intval($this->memberBirthday->diff(new DateTime())->format('y'));

        return $format ? $age . ' ans' : $age;
    }

    /**
     * @param DateTime $set
     *
     * @return $this
     */
    public function setMemberBirthday(DateTime $set): self
    {
        $this->memberBirthday = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string
     */
    public function getMemberStartPractice(bool $format = false): DateTime|string
    {
        return $format ? $this->memberStartPractice->format('d/m/Y') : $this->memberStartPractice;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getMemberPracticeTime(bool $format = false): int|string
    {
        $age = intval($this->memberStartPractice->diff(new DateTime())->format('y'));

        return $format ? ($age > 1 ? $age . ' années' : $age . ' année') : $age;
    }

    /**
     * @param DateTime $set
     *
     * @return $this
     */
    public function setMemberStartPractice(DateTime $set): self
    {
        $this->memberStartPractice = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getMemberAikikaiId(bool $format = false): string|null
    {
        if (is_null($this->memberAikikaiId))
        {
            return $format ? 'Aucun' : null;
        }

        return $this->memberAikikaiId;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setMemberAikikaiId(string|null $set = null): self
    {
        $this->memberAikikaiId = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getMemberSubscriptionStatus(bool $format = false): int|string
    {
        return $format ? $this->getMemberSubscriptionStatusText($this->memberSubscriptionStatus) : $this->memberSubscriptionStatus;
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getMemberSubscriptionStatusText(int $id = 0): array|string
    {
        $keys = array('N\'expire jamais' => 1, 'Date d\'expiration' => 2, 'Ne pratique plus' => 3);

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
     * @param int $set
     *
     * @return $this
     */
    public function setMemberSubscriptionStatus(int $set = 1): self
    {
        $this->memberSubscriptionStatus = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getMemberSubscriptionValidity(bool $format = false): DateTime|string|null
    {
        if (is_null($this->memberSubscriptionValidity))
        {
            return $format ? 'Aucune' : null;
        }

        return $format ? $this->memberSubscriptionValidity->format('d/m/Y') : $this->memberSubscriptionValidity;
    }

    /**
     * @param DateTime|null $set
     *
     * @return $this
     */
    public function setMemberSubscriptionValidity(DateTime|null $set = null): self
    {
        $this->memberSubscriptionValidity = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getMemberSubscriptionList(bool $format = false): int|string
    {
        return $format ? $this->getMemberSubscriptionListText($this->memberSubscriptionList) : $this->memberSubscriptionList;
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getMemberSubscriptionListText(int $id = 0): array|string
    {
        $keys = array('Cours Adultes' => 1, 'Cours Enfants' => 2, 'Cours Adultes/Enfants' => 3);

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
     * @param int $set
     *
     * @return $this
     */
    public function setMemberSubscriptionList(int $set = 1): self
    {
        $this->memberSubscriptionList = $set;

        return $this;
    }

    /**
     * @return bool
     */
    public function getMemberLastKagami(): bool
    {
        return $this->memberLastKagami;
    }

    /**
     * @param bool $set
     *
     * @return $this
     */
    public function setMemberLastKagami(bool $set = false): self
    {
        $this->memberLastKagami = $set;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberClubLessonAttendances(): Collection
    {
        return $this->memberClubLessonAttendances;
    }

    /**
     * @return Collection
     */
    public function getMemberClubManagers(): Collection
    {
        return $this->memberClubManagers;
    }

    /**
     * @return Collection
     */
    public function getMemberClubTeachers(): Collection
    {
        return $this->memberClubTeachers;
    }

    /**
     * @return Collection
     */
    public function getMemberClusterMembers(): Collection
    {
        return $this->memberClusterMembers;
    }

    /**
     * @return bool
     */
    public function getMemberFreeTraining(): bool
    {
        foreach ($this->getMemberClusterMembers() as $cluster)
        {
            if ($cluster->getClusterMembercluster()->getClusterFreeTraining() && $cluster->getClusterMemberActive())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Collection
     */
    public function getMemberFormations(): Collection
    {
        return $this->memberFormations;
    }

    /**
     * @return Formation|null
     */
    public function getMemberLastFormation(): Formation|null
    {
        return $this->memberFormations[0];
    }

    /**
     * @return Collection
     */
    public function getMemberFormationSessionCandidates(): Collection
    {
        return $this->memberFormationSessionCandidates;
    }

    /**
     * @return Collection
     */
    public function getMemberGrades(): Collection
    {
        return $this->memberGrades;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getMemberAikikaiDate(bool $format = false): DateTime|string|null
    {
        foreach ($this->memberGrades as $grade)
        {
            if (($grade->getGradeRank() == 8) && ($grade->getGradeStatus() < 4))
            {
                return $grade->getGradeDate($format);
            }
        }

        return $format ? 'Aucune' : null;
    }

    /**
     * @param bool $format
     *
     * @return Grade|string|null
     */
    public function getMemberLastGrade(bool $format = false): Grade|string|null
    {
        foreach ($this->memberGrades as $grade)
        {
            if ($grade->getGradeStatus() < 4)
            {
                return $format ? $grade->getGradeRank($format) : $grade;
            }
        }

        return $format ? $this->memberGrades[0]->getGradeRank($format) : $this->memberGrades[0];
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getMemberLastGradeDate(bool $format = false): DateTime|string|null
    {
        return $this->getMemberLastGrade()->getGradeDate($format);
    }

    /**
     * @param bool $format
     *
     * @return Grade|string|null
     */
    public function getMemberLastGradeExam(bool $format = false): Grade|string|null
    {
        foreach ($this->memberGrades as $grade)
        {
            if ($grade->getGradeStatus() == 1)
            {
                return $format ? $grade->getGradeRank($format) : $grade;
            }
        }

        return $format ? 'Aucun' : null;
    }

    /**
     * @param bool $format
     *
     * @return Grade|string|null
     */
    public function getMemberLastGradeAikikai(bool $format = false): Grade|string|null
    {
        foreach ($this->memberGrades as $grade)
        {
            if ($grade->getGradeRank() <= 6)
            {
                break;
            }

            if ($grade->getGradeStatus() == 4)
            {
                continue;
            }

            if ($grade->getGradeRank() % 2 == 0)
            {
                return $format ? $grade->getGradeRank($format) : $grade;
            }
        }

        return $format ? 'Aucun' : null;
    }

    /**
     * @param bool $format
     *
     * @return Grade|string|null
     */
    public function getMemberLastGradeAFA(bool $format = false): Grade|string|null
    {
        foreach ($this->memberGrades as $grade)
        {
            if ($grade->getGradeRank() <= 6)
            {
                return $grade;
            }

            if ($grade->getGradeStatus() == 4)
            {
                continue;
            }

            if ($grade->getGradeRank() % 2 != 0)
            {
                return $format ? $grade->getGradeRank($format) : $grade;
            }
        }

        return $format ? 'Aucun' : null;
    }

    /**
     * @return Collection
     */
    public function getMemberGradeSessionCandidates(): Collection
    {
        return $this->memberGradeSessionCandidates;
    }

    /**
     * @return Grade|null
     */
    public function getMemberLastExamSession(): GradeSessionCandidate|null
    {
        return $this->memberGradeSessionCandidates[0];
    }

    /**
     * @return Collection
     */
    public function getMemberLicences(): Collection
    {
        return $this->memberLicences;
    }

    /**
     * @return MemberLicence|null
     */
    public function getMemberLastLicence(): MemberLicence|null
    {
        return $this->getMemberLicences()[0];
    }

    /**
     * @param bool $format
     *
     * @return Club|string|null
     */
    public function getMemberActualClub(bool $format = false): Club|string|null
    {
        if (sizeof($this->getMemberLicences()) > 0)
        {
            return $format ? $this->getMemberLastLicence()->getMemberLicenceClub()->getClubName($format) : $this->getMemberLastLicence()->getMemberLicenceClub();
        }

        return $format ? 'Aucun' : null;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getMemberDeadline(bool $format = false): DateTime|string|null
    {
        return $this->getMemberLastLicence()->getMemberLicenceDeadline($format);
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getMemberLicenceUpdate(bool $format = false): DateTime|string|null
    {
        return $this->getMemberLastLicence()->getMemberLicenceUpdate($format);
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getMemberLicencePayment(bool $format = false): DateTime|string|null
    {
        return $this->getMemberLastLicence()->getMemberLicencePaymentDate($format);
    }

    /**
     * @return bool
     */
    public function getMemberNeedRenew(): bool
    {
        if ($this->getMemberDeadline() > new DateTime('+3 month today'))
        {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function getMemberOutdate(): bool
    {
        if ($this->getMemberDeadline() > new DateTime('-3 month today'))
        {
            return false;
        }

        return true;
    }

    /**
     * @return Collection
     */
    public function getMemberTitles(): Collection
    {
        return $this->memberTitles;
    }

    /**
     * @param bool $format
     *
     * @return Title|string|null
     */
    public function getMemberLastTitle(bool $format = false): Title|string|null
    {
        if (sizeof($this->memberTitles) > 0)
        {
            return $format ? $this->memberTitles[0]->getTitleRank($format) : $this->memberTitles[0];
        }

        return $format ? 'Aucun' : null;
    }

    /**
     * @return Collection
     */
    public function getMemberTrainingAttendances(): Collection
    {
        return $this->memberTrainingAttendances;
    }

    /**
     * @return User|null
     */
    public function getMemberUser(): User|null
    {
        return $this->memberUser;
    }

    /**
     * @param User|null $user
     *
     * @return $this
     */
    public function setMemberUser(User|null $user): self
    {
        $this->memberUser = $user;

        return $this;
    }
}
