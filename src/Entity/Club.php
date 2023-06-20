<?php
// src/Entity/Club.php
namespace App\Entity;

use App\Repository\ClubRepository;

use App\Service\ListData;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Mime\Address;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Club
 */
#[ORM\Table(name: 'club')]
#[ORM\Entity(repositoryClass: ClubRepository::class)]
class Club
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    private int $club_id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $club_name;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_address;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $club_zip;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_city;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $club_province;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $club_creation;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $club_type;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_bce_number;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_iban;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Url]
    private ?string $club_url;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Url]
    private ?string $club_facebook;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Url]
    private ?string $club_instagram;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Url]
    private ?string $club_youtube;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_email_public;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_phone_public;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_contact_public;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_president;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_secretary;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_treasurer;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'club_class_club', targetEntity: ClubClass::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['club_class_day' => 'ASC', 'club_class_starting_hour' => 'ASC'])]
    private Collection|ArrayCollection|null $club_classes;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'club_dojo_club', targetEntity: ClubDojo::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection|null $club_dojos;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'grade_club', targetEntity: Grade::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection|null $club_grades;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'club_history', targetEntity: ClubHistory::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['club_history_update' => 'DESC'])]
    private Collection|ArrayCollection|null $club_histories;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'lesson_club', targetEntity: Lesson::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection|null $club_lessons;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'member_licence_club', targetEntity: MemberLicence::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection|null $club_licences;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'club_manager_club', targetEntity: ClubManager::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['club_manager_is_main' => 'DESC'])]
    private Collection|ArrayCollection|null $club_managers;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'club_teacher', targetEntity: ClubTeacher::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['club_teacher_title' => 'ASC'])]
    private Collection|ArrayCollection|null $club_teachers;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'training_club', targetEntity: Training::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection|null $club_trainings;

    /**
     * Club constructor.
     */
    public function __construct()
    {
        $this->club_dojos     = new ArrayCollection();
        $this->club_grades    = new ArrayCollection();
        $this->club_histories = new ArrayCollection();
        $this->club_lessons   = new ArrayCollection();
        $this->club_licences  = new ArrayCollection();
        $this->club_managers  = new ArrayCollection();
        $this->club_teachers  = new ArrayCollection();
        $this->club_trainings = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getClubId(): ?int
    {
        return $this->club_id;
    }

    /**
     * @param int $club_id
     * @return $this
     */
    public function setClubId(int $club_id): self
    {
        $this->club_id = $club_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubName(): ?string
    {
        return $this->club_name;
    }

    /**
     * @param string $club_name
     * @return $this
     */
    public function setClubName(string $club_name): self
    {
        $this->club_name = $club_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubAddress(): ?string
    {
        return $this->club_address;
    }

    /**
     * @param string|null $club_address
     * @return $this
     */
    public function setClubAddress(?string $club_address): self
    {
        $this->club_address = $club_address;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubZip(): ?int
    {
        return $this->club_zip;
    }

    /**
     * @param int|null $club_zip
     * @return $this
     */
    public function setClubZip(?int $club_zip): self
    {
        $this->club_zip = $club_zip;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubCity(): ?string
    {
        return $this->club_city;
    }

    /**
     * @param string|null $club_city
     * @return $this
     */
    public function setClubCity(?string $club_city): self
    {
        $this->club_city = $club_city;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubProvince(): ?int
    {
        return $this->club_province;
    }

    /**
     * @param int|null $club_province
     * @return $this
     */
    public function setClubProvince(?int $club_province): self
    {
        $this->club_province = $club_province;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClubCreation(): ?DateTime
    {
        return $this->club_creation;
    }

    /**
     * @param DateTime|null $club_creation
     * @return $this
     */
    public function setClubCreation(?DateTime $club_creation): self
    {
        $this->club_creation = $club_creation;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubType(): ?int
    {
        return $this->club_type;
    }

    /**
     * @param int|null $club_type
     * @return $this
     */
    public function setClubType(?int $club_type): self
    {
        $this->club_type = $club_type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubBceNumber(): ?string
    {
        return $this->club_bce_number;
    }

    /**
     * @param string|null $club_bce_number
     * @return $this
     */
    public function setClubBceNumber(?string $club_bce_number): self
    {
        $this->club_bce_number = $club_bce_number;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubIban(): ?string
    {
        return $this->club_iban;
    }

    /**
     * @param string|null $club_iban
     * @return $this
     */
    public function setClubIban(?string $club_iban): self
    {
        $this->club_iban = $club_iban;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubUrl(): ?string
    {
        return $this->club_url;
    }

    /**
     * @param string|null $club_url
     * @return $this
     */
    public function setClubUrl(?string $club_url): self
    {
        $this->club_url = $club_url;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubFacebook(): ?string
    {
        return $this->club_facebook;
    }

    /**
     * @param string|null $club_facebook
     * @return $this
     */
    public function setClubFacebook(?string $club_facebook): self
    {
        $this->club_facebook = $club_facebook;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubInstagram(): ?string
    {
        return $this->club_instagram;
    }

    /**
     * @param string|null $club_instagram
     * @return $this
     */
    public function setClubInstagram(?string $club_instagram): self
    {
        $this->club_instagram = $club_instagram;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubYoutube(): ?string
    {
        return $this->club_youtube;
    }

    /**
     * @param string|null $club_youtube
     * @return $this
     */
    public function setClubYoutube(?string $club_youtube): self
    {
        $this->club_youtube = $club_youtube;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubEmailPublic(): ?string
    {
        return $this->club_email_public;
    }

    /**
     * @param string|null $club_email_public
     * @return $this
     */
    public function setClubEmailPublic(?string $club_email_public): self
    {
        $this->club_email_public = $club_email_public;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubPhonePublic(): ?string
    {
        return $this->club_phone_public;
    }

    /**
     * @param string|null $club_phone_public
     * @return $this
     */
    public function setClubPhonePublic(?string $club_phone_public): self
    {
        $this->club_phone_public = $club_phone_public;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubContactPublic(): ?string
    {
        return $this->club_contact_public;
    }

    /**
     * @param string|null $club_contact_public
     * @return $this
     */
    public function setClubContactPublic(?string $club_contact_public): self
    {
        $this->club_contact_public = $club_contact_public;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubPresident(): ?string
    {
        return $this->club_president;
    }

    /**
     * @param string|null $club_president
     * @return $this
     */
    public function setClubPresident(?string $club_president): self
    {
        $this->club_president = $club_president;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubSecretary(): ?string
    {
        return $this->club_secretary;
    }

    /**
     * @param string|null $club_secretary
     * @return $this
     */
    public function setClubSecretary(?string $club_secretary): self
    {
        $this->club_secretary = $club_secretary;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubTreasurer(): ?string
    {
        return $this->club_treasurer;
    }

    /**
     * @param string|null $club_treasurer
     * @return $this
     */
    public function setClubTreasurer(?string $club_treasurer): self
    {
        $this->club_treasurer = $club_treasurer;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubClasses(): Collection
    {
        return $this->club_classes;
    }

    /**
     * @param ClubClass $club_class
     * @return $this
     */
    public function addClubClasses(ClubClass $club_class): self
    {
        if (!$this->club_classes->contains($club_class)) {
            $this->club_classes[] = $club_class;
            $club_class->setClubClassClub($this);
        }

        return $this;
    }

    /**
     * @param ClubClass $club_class
     * @return $this
     */
    public function removeClubClasses(ClubClass $club_class): self
    {
        if ($this->club_classes->contains($club_class)) {
            $this->club_classes->removeElement($club_class);
            // set the owning side to null (unless already changed)
            if ($club_class->getClubClassClub() === $this) {
                $club_class->setClubClassClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubDojos(): Collection
    {
        return $this->club_dojos;
    }

    /**
     * @param ClubDojo $clubDojo
     * @return $this
     */
    public function addClubDojos(ClubDojo $clubDojo): self
    {
        if (!$this->club_dojos->contains($clubDojo)) {
            $this->club_dojos[] = $clubDojo;
            $clubDojo->setClubDojoClub($this);
        }

        return $this;
    }

    /**
     * @param ClubDojo $clubDojo
     * @return $this
     */
    public function removeClubDojos(ClubDojo $clubDojo): self
    {
        if ($this->club_dojos->contains($clubDojo)) {
            $this->club_dojos->removeElement($clubDojo);
            // set the owning side to null (unless already changed)
            if ($clubDojo->getClubDojoClub() === $this) {
                $clubDojo->setClubDojoClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubGrades(): Collection
    {
        return $this->club_grades;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function addClubGrades(Grade $grade): self
    {
        if (!$this->club_grades->contains($grade)) {
            $this->club_grades[] = $grade;
            $grade->setGradeClub($this);
        }

        return $this;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function removeClubGrades(Grade $grade): self
    {
        if ($this->club_grades->contains($grade)) {
            $this->club_grades->removeElement($grade);
            // set the owning side to null (unless already changed)
            if ($grade->getGradeClub() === $this) {
                $grade->setGradeClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubHistories(): Collection
    {
        return $this->club_histories;
    }

    /**
     * @param ClubHistory $clubHistory
     * @return $this
     */
    public function addClubHistories(ClubHistory $clubHistory): self
    {
        if (!$this->club_histories->contains($clubHistory)) {
            $this->club_histories[] = $clubHistory;
            $clubHistory->setClubHistory($this);
        }

        return $this;
    }

    /**
     * @param ClubHistory $clubHistory
     * @return $this
     */
    public function removeClubHistories(ClubHistory $clubHistory): self
    {
        if ($this->club_histories->contains($clubHistory)) {
            $this->club_histories->removeElement($clubHistory);
            // set the owning side to null (unless already changed)
            if ($clubHistory->getClubHistory() === $this) {
                $clubHistory->setClubHistory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubLessons(): Collection
    {
        return $this->club_lessons;
    }

    /**
     * @param Lesson $lesson
     * @return $this
     */
    public function addClubLessons(Lesson $lesson): self
    {
        if (!$this->club_lessons->contains($lesson)) {
            $this->club_lessons[] = $lesson;
            $lesson->setLessonClub($this);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubLicences(): Collection
    {
        return $this->club_licences;
    }

    /**
     * @param MemberLicence $memberLicence
     * @return $this
     */
    public function addClubLicences(MemberLicence $memberLicence): self
    {
        if (!$this->club_licences->contains($memberLicence)) {
            $this->club_licences[] = $memberLicence;
            $memberLicence->setMemberLicenceClub($this);
        }

        return $this;
    }

    /**
     * @param MemberLicence $memberLicence
     * @return $this
     */
    public function removeClubLicences(MemberLicence $memberLicence): self
    {
        if ($this->club_licences->contains($memberLicence)) {
            $this->club_licences->removeElement($memberLicence);
            // set the owning side to null (unless already changed)
            if ($memberLicence->getMemberLicenceClub() === $this) {
                $memberLicence->setMemberLicenceClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubManagers(): Collection
    {
        return $this->club_managers;
    }

    /**
     * @param ClubManager $clubManager
     * @return $this
     */
    public function addClubManagers(ClubManager $clubManager): self
    {
        if (!$this->club_managers->contains($clubManager)) {
            $this->club_managers[] = $clubManager;
            $clubManager->setClubManagerClub($this);
        }

        return $this;
    }

    /**
     * @param ClubManager $clubManager
     * @return $this
     */
    public function removeClubManagers(ClubManager $clubManager): self
    {
        if ($this->club_managers->contains($clubManager)) {
            $this->club_managers->removeElement($clubTeacher);
            // set the owning side to null (unless already changed)
            if ($clubManager->getClubManagerClub() === $this) {
                $clubManager->setClubManagerClub(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubTeachers(): Collection
    {
        return $this->club_teachers;
    }

    /**
     * @param ClubTeacher $clubTeacher
     * @return $this
     */
    public function addClubTeachers(ClubTeacher $clubTeacher): self
    {
        if (!$this->club_teachers->contains($clubTeacher)) {
            $this->club_teachers[] = $clubTeacher;
            $clubTeacher->setClubTeacher($this);
        }

        return $this;
    }

    /**
     * @param ClubTeacher $clubTeacher
     * @return $this
     */
    public function removeClubTeachers(ClubTeacher $clubTeacher): self
    {
        if ($this->club_teachers->contains($clubTeacher))
        {
            $this->club_teachers->removeElement($clubTeacher);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubTrainings(): Collection
    {
        return $this->club_trainings;
    }

    /**
     * @param Training $training
     * @return $this
     */
    public function addClubTrainings(Training $training): self
    {
        if (!$this->club_trainings->contains($training)) {
            $this->club_trainings[] = $training;
            $training->setTrainingClub($this);
        }

        return $this;
    }

    /**
     * @param Training $training
     * @return $this
     */
    public function removeClubTrainings(Training $training): self
    {
        if ($this->club_trainings->contains($training)) {
            $this->club_trainings->removeElement($training);
            // set the owning side to null (unless already changed)
            if ($training->getTrainingClub() === $this) {
                $training->setTrainingClub(null);
            }
        }

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return string
     */
    public function getClubTypeName(): string
    {
        $listData = new ListData();

        return $listData->getClubType($this->getClubType());
    }

    /**
     * @return string
     */
    public function getClubIbanDisplay(): string
    {
        if (is_null($this->getClubIban()))
        {
            return 'Aucun';
        }

        return $this->getClubIban();
    }

    /**
     * @return string
     */
    public function getClubBceNumberDisplay(): string
    {
        if (is_null($this->getClubBceNumber()))
        {
            return 'Aucun';
        }

        return $this->getClubBceNumber();
    }

    /**
     * @return string
     */
    public function getClubPresidentDisplay(): string
    {
        if (is_null($this->getClubPresident()))
        {
            return 'Non défini';
        }

        return $this->getClubPresident();
    }

    /**
     * @return string
     */
    public function getClubSecretaryDisplay(): string
    {
        if (is_null($this->getClubSecretary()))
        {
            return 'Non défini';
        }

        return $this->getClubSecretary();
    }

    /**
     * @return string
     */
    public function getClubTreasurerDisplay(): string
    {
        if (is_null($this->getClubTreasurer()))
        {
            return 'Non défini';
        }

        return $this->getClubTreasurer();
    }

    /**
     * @return string
     */
    public function getClubUrlDisplay(): string
    {
        if (is_null($this->getClubUrl()))
        {
            return 'Aucune';
        }

        return $this->getClubUrl();
    }

    /**
     * @return string
     */
    public function getClubContactPublicDisplay(): string
    {
        if (is_null($this->getClubContactPublic()))
        {
            return 'Aucun';
        }

        return $this->getClubContactPublic();
    }

    /**
     * @return string
     */
    public function getClubPhonePublicDisplay(): string
    {
        if (is_null($this->getClubPhonePublic()))
        {
            return 'Aucun';
        }

        return $this->getClubPhonePublic();
    }

    /**
     * @return string
     */
    public function getClubEmailPublicDisplay(): string
    {
        if (is_null($this->getClubEmailPublic()))
        {
            return 'Aucun';
        }

        return $this->getClubEmailPublic();
    }

    /**
     * @return string
     */
    public function getClubFacebookDisplay(): string
    {
        if (is_null($this->getClubFacebook()))
        {
            return 'Aucune';
        }

        return $this->getClubFacebook();
    }

    /**
     * @return string
     */
    public function getClubInstagramDisplay(): string
    {
        if (is_null($this->getClubInstagram()))
        {
            return 'Aucune';
        }

        return $this->getClubInstagram();
    }

    /**
     * @return string
     */
    public function getClubYoutubeDisplay(): string
    {
        if (is_null($this->getClubYoutube()))
        {
            return 'Aucune';
        }

        return $this->getClubYoutube();
    }

    /**
     * @return bool
     */
    public function getClubIsActive(): bool
    {
        if ($this->getClubHistories()[0]->getClubHistoryStatus() == 1)
        {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getClubDojoCho(): array
    {
        $dojoCho = array();

        foreach ($this->getClubTeachers() as $teacher)
        {
            if ($teacher->getClubTeacherTitle() == 1)
            {
                $dojoCho[] = $teacher;
            }
        }

        return $dojoCho;
    }

    /**
     * @param bool $format
     * @return array
     */
    public function getClubDojoChoEmail(bool $format = false): array
    {
        $email = array();

        foreach ($this->getClubDojoCho() as $teacher)
        {
            if (!is_null($teacher->getClubTeacherMember()?->getMemberEmail()))
            {
                $email[$teacher->getClubTeacherMember()->getMemberEmail()] = new Address($teacher->getClubTeacherMember()->getMemberEmail(), ucwords($teacher->getClubTeacherMember()->getMemberFirstname()) . ' ' . ucwords($teacher->getClubTeacherMember()->getMemberName()));
            }
        }

        return $format ? $email : array_keys($email);
    }

    /**
     * @param bool $format
     * @return array
     */
    public function getClubManagerMail(bool $format = false): array
    {
        $email = array();

        foreach ($this->getClubManagers() as $manager)
        {
            if (is_null($manager->getClubManagerMember()))
            {
                $email[$manager->getClubManagerUser()->getUserEmail()] = new Address($manager->getClubManagerUser()->getUserEmail(), ucwords($manager->getClubManagerUser()->getUserFirstname()) . ' ' . ucwords($manager->getClubManagerUser()->getUserRealName()));
            }
            else
            {
                if (!is_null($manager->getClubManagerMember()->getMemberEmail()))
                {
                    $email[$manager->getClubManagerMember()->getMemberEmail()] = new Address($manager->getClubManagerMember()->getMemberEmail(), ucwords($manager->getClubManagerMember()->getMemberFirstname()) . ' ' . ucwords($manager->getClubManagerMember()->getMemberName()));
                }
            }
        }

        return $format ? $email : array_keys($email);
    }

    /**
     * @param bool $format
     * @return string|Address
     */
    public function getClubMainManagerMail(bool $format = false): string|Address
    {
        $manager = $this->getClubManagers()[0];

        if (is_null($manager->getClubManagerMember()))
        {
            if ($format)
            {
                $email = new Address($manager->getClubManagerUser()->getUserEmail(), ucwords($manager->getClubManagerUser()->getUserFirstname()) . ' ' . ucwords($manager->getClubManagerUser()->getUserRealName()));
            }
            else
            {
                $email = $manager->getClubManagerUser()->getUserEmail();
            }
        }
        else
        {
            if (!is_null($manager->getClubManagerMember()->getMemberEmail()))
            {
                if ($format)
                {
                    $email = new Address($manager->getClubManagerMember()->getMemberEmail(), ucwords($manager->getClubManagerMember()->getMemberFirstname()) . ' ' . ucwords($manager->getClubManagerMember()->getMemberName()));
                }
                else
                {
                    $email = $manager->getClubManagerMember()->getMemberEmail();
                }
            }
        }

        if (!isset($email))
        {
            if ($format)
            {
                $email = new Address('afa@aikido.be', 'Secrétariat AFA');
            }
            else
            {
                $email = 'afa@aikido.be';
            }
        }

        return $email;
    }

    /**
     * @return string
     */
    public function getClubMainManagerName(): string
    {
        $manager = $this->getClubManagers()[0];

        if (is_null($manager->getClubManagerMember()))
        {
            $name = ucwords($manager->getClubManagerUser()->getUserFirstname()) . ' ' . ucwords($manager->getClubManagerUser()->getUserRealName());
        }
        else
        {
            $name = ucwords($manager->getClubManagerMember()->getMemberFirstname()) . ' ' . ucwords($manager->getClubManagerMember()->getMemberName());
        }

        return $name;
    }

    /**
     * @param bool $format
     * @return array
     */
    public function getClubStaffEmail(bool $format = false): array
    {
        $email = array_merge($this->getClubDojoChoEmail(true), $this->getClubManagerMail(true));

        return $format ? $email : array_keys($email);
    }
}
