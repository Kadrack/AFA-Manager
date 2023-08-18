<?php
// src/Entity/Member.php
namespace App\Entity;

use App\Repository\MemberRepository;

use App\Service\ListData;
use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

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
    private int $member_id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $member_firstname;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $member_name;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $member_photo;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $member_sex;

    /**
     * @var string
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $member_address;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private ?string $member_zip;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $member_city;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $member_country;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Email]
    private ?string $member_email;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $member_phone;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $member_birthday;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $member_start_practice;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $member_aikikai_id;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $member_subscription_status = 1;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $member_subscription_validity;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $member_subscription_list = 1;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'cluster_member', targetEntity: ClusterMember::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $member_clusters;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'grade_session_candidate_member', targetEntity: GradeSessionCandidate::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['grade_session_candidate_rank' => 'DESC'])]
    private ArrayCollection|Collection|null $member_exams;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'formation_member', targetEntity: Formation::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['formation_date' => 'DESC'])]
    private ArrayCollection|Collection|null $member_formations;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'grade_member', targetEntity: Grade::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['grade_rank' => 'DESC', 'grade_session' => 'DESC'])]
    private ArrayCollection|Collection|null $member_grades;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'lesson_attendance_member', targetEntity: LessonAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $member_lesson_attendances;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'member_licence', targetEntity: MemberLicence::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['member_licence_id' => 'DESC'])]
    private ArrayCollection|Collection|null $member_licences;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'club_manager_member', targetEntity: ClubManager::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $member_managers;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'club_teacher_member', targetEntity: ClubTeacher::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $member_teachers;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'title_member', targetEntity: Title::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['title_date' => 'DESC'])]
    private ArrayCollection|Collection|null $member_titles;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'training_attendance_member', targetEntity: TrainingAttendance::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $member_training_attendances;

    /**
     * @var User|null
     */
    #[ORM\OneToOne(mappedBy: 'user_member', targetEntity: User::class, cascade: ['persist'], orphanRemoval: true)]
    private User|null $member_user;

    /**
     * Member constructor.
     */
    public function __construct()
    {
        $this->member_clusters             = new ArrayCollection();
        $this->member_exams                = new ArrayCollection();
        $this->member_formations           = new ArrayCollection();
        $this->member_grades               = new ArrayCollection();
        $this->member_lesson_attendances   = new ArrayCollection();
        $this->member_licences             = new ArrayCollection();
        $this->member_managers             = new ArrayCollection();
        $this->member_teachers             = new ArrayCollection();
        $this->member_titles               = new ArrayCollection();
        $this->member_training_attendances = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getMemberId(): int
    {
        return $this->member_id;
    }

    /**
     * @param int $member_id
     * @return $this
     */
    public function setMemberId(int $member_id): self
    {
        $this->member_id = $member_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberFirstname(): string
    {
        return ucwords(strtolower($this->member_firstname));
    }

    /**
     * @param string $member_firstname
     * @return $this
     */
    public function setMemberFirstname(string $member_firstname): self
    {
        $this->member_firstname = ucwords($member_firstname);

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberName(): string
    {
        return ucwords(strtolower($this->member_name));
    }

    /**
     * @param string $member_name
     * @return $this
     */
    public function setMemberName(string $member_name): self
    {
        $this->member_name = ucwords($member_name);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberPhoto(): ?string
    {
        return $this->member_photo;
    }

    /**
     * @param string|null $member_photo
     * @return $this
     */
    public function setMemberPhoto(?string $member_photo): self
    {
        $this->member_photo = $member_photo;

        return $this;
    }

    /**
     * @return int
     */
    public function getMemberSex(): int
    {
        return $this->member_sex;
    }

    public function setMemberSex(int $member_sex): self
    {
        $this->member_sex = $member_sex;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberAddress(): string
    {
        return $this->member_address;
    }

    /**
     * @param string $member_address
     * @return $this
     */
    public function setMemberAddress(string $member_address): self
    {
        $this->member_address = $member_address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberZip(): ?string
    {
        return $this->member_zip;
    }

    /**
     * @param string $member_zip
     * @return $this
     */
    public function setMemberZip(string $member_zip): self
    {
        $this->member_zip = $member_zip;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberCity(): string
    {
        return $this->member_city;
    }

    /**
     * @param string $member_city
     * @return $this
     */
    public function setMemberCity(string $member_city): self
    {
        $this->member_city = $member_city;

        return $this;
    }

    /**
     * @return string
     */
    public function getMemberCountry(): string
    {
        return $this->member_country;
    }

    /**
     * @param string $member_country
     * @return $this
     */
    public function setMemberCountry(string $member_country): self
    {
        $this->member_country = $member_country;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberEmail(): ?string
    {
        return $this->member_email;
    }

    /**
     * @param string|null $member_email
     * @return $this
     */
    public function setMemberEmail(?string $member_email): self
    {
        $this->member_email = $member_email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberPhone(): ?string
    {
        return $this->member_phone;
    }

    /**
     * @param string|null $member_phone
     * @return $this
     */
    public function setMemberPhone(?string $member_phone): self
    {
        $this->member_phone = $member_phone;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMemberBirthday(): DateTime
    {
        return $this->member_birthday;
    }

    /**
     * @param DateTime $member_birthday
     * @return $this
     */
    public function setMemberBirthday(DateTime $member_birthday): self
    {
        $this->member_birthday = $member_birthday;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getMemberStartPractice(): DateTime
    {
        return $this->member_start_practice;
    }

    /**
     * @param DateTime $member_start_practice
     * @return $this
     */
    public function setMemberStartPractice(DateTime $member_start_practice): self
    {
        $this->member_start_practice = $member_start_practice;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMemberAikikaiId(): ?string
    {
        return $this->member_aikikai_id;
    }

    /**
     * @param string|null $member_aikikai_id
     * @return $this
     */
    public function setMemberAikikaiId(?string $member_aikikai_id): self
    {
        $this->member_aikikai_id = $member_aikikai_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getMemberSubscriptionStatus(): int
    {
        return $this->member_subscription_status;
    }

    /**
     * @param int|null $member_subscription_status
     * @return $this
     */
    public function setMemberSubscriptionStatus(?int $member_subscription_status): self
    {
        $this->member_subscription_status = $member_subscription_status;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getMemberSubscriptionValidity(): ?DateTime
    {
        return $this->member_subscription_validity;
    }

    /**
     * @param DateTime|null $member_subscription_validity
     * @return $this
     */
    public function setMemberSubscriptionValidity(?DateTime $member_subscription_validity): self
    {
        $this->member_subscription_validity = $member_subscription_validity;

        return $this;
    }

    /**
     * @return int
     */
    public function getMemberSubscriptionList(): int
    {
        return $this->member_subscription_list;
    }

    /**
     * @param int|null $member_subscription_list
     * @return $this
     */
    public function setMemberSubscriptionList(?int $member_subscription_list): self
    {
        $this->member_subscription_list = $member_subscription_list;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberClusters(): Collection
    {
        return $this->member_clusters;
    }

    /**
     * @param ClusterMember $clusterMember
     * @return $this
     */
    public function addMemberClusters(ClusterMember $clusterMember): self
    {
        if (!$this->member_clusters->contains($clusterMember)) {
            $this->member_clusters[] = $clusterMember;
            $clusterMember->setClusterMember($this);
        }

        return $this;
    }

    /**
     * @param ClusterMember $clusterMember
     * @return $this
     */
    public function removeMemberClusters(ClusterMember $clusterMember): self
    {
        if ($this->member_clusters->contains($clusterMember)) {
            $this->member_clusters->removeElement($clusterMember);
            // set the owning side to null (unless already changed)
            if ($clusterMember->getClusterMember() === $this) {
                $clusterMember->setClusterMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberExams(): Collection
    {
        return $this->member_exams;
    }

    /**
     * @param GradeSessionCandidate $gradeSessionCandidate
     * @return $this
     */
    public function addMemberExams(GradeSessionCandidate $gradeSessionCandidate): self
    {
        if (!$this->member_exams->contains($gradeSessionCandidate)) {
            $this->member_exams[] = $gradeSessionCandidate;
            $gradeSessionCandidate->setGradeSessionCandidateMember($this);
        }

        return $this;
    }

    /**
     * @param GradeSessionCandidate $gradeSessionCandidate
     * @return $this
     */
    public function removeMemberExams(GradeSessionCandidate $gradeSessionCandidate): self
    {
        if ($this->member_exams->contains($gradeSessionCandidate)) {
            $this->member_exams->removeElement($gradeSessionCandidate);
            // set the owning side to null (unless already changed)
            if ($gradeSessionCandidate->getGradeSessionCandidateMember() === $this) {
                $gradeSessionCandidate->setGradeSessionCandidateMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberFormations(): Collection
    {
        return $this->member_formations;
    }

    /**
     * @param Formation $formation
     * @return $this
     */
    public function addMemberFormations(Formation $formation): self
    {
        if (!$this->member_formations->contains($formation)) {
            $this->member_formations[] = $formation;
            $formation->setFormationMember($this);
        }

        return $this;
    }

    /**
     * @param Formation $formation
     * @return $this
     */
    public function removeMemberFormations(Formation $formation): self
    {
        if ($this->member_formations->contains($formation))
        {
            $this->member_formations->removeElement($formation);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberGrades(): Collection
    {
        return $this->member_grades;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function addMemberGrades(Grade $grade): self
    {
        if (!$this->member_grades->contains($grade)) {
            $this->member_grades[] = $grade;
            $grade->setGradeMember($this);
        }

        return $this;
    }

    /**
     * @param Grade $grade
     * @return $this
     */
    public function removeMemberGrades(Grade $grade): self
    {
        if ($this->member_grades->contains($grade))
        {
            $this->member_grades->removeElement($grade);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberLessonAttendances(): Collection
    {
        return $this->member_lesson_attendances;
    }

    /**
     * @param LessonAttendance $lesson_attendance
     * @return $this
     */
    public function addMemberLessonAttendances(LessonAttendance $lesson_attendance): self
    {
        if (!$this->member_lesson_attendances->contains($lesson_attendance)) {
            $this->member_lesson_attendances[] = $lesson_attendance;
            $lesson_attendance->setLessonAttendanceMember($this);
        }

        return $this;
    }

    /**
     * @param LessonAttendance $lesson_attendance
     * @return $this
     */
    public function removeMemberLessonAttendances(LessonAttendance $lesson_attendance): self
    {
        if ($this->member_lesson_attendances->contains($lesson_attendance)) {
            $this->member_lesson_attendances->removeElement($lesson_attendance);
            // set the owning side to null (unless already changed)
            if ($lesson_attendance->getLessonAttendanceMember() === $this) {
                $lesson_attendance->setLessonAttendanceMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberLicences(): Collection
    {
        return $this->member_licences;
    }

    /**
     * @param MemberLicence $memberLicence
     * @return $this
     */
    public function addMemberLicences(MemberLicence $memberLicence): self
    {
        if (!$this->member_licences->contains($memberLicence)) {
            $this->member_licences[] = $memberLicence;
            $memberLicence->setMemberLicence($this);
        }

        return $this;
    }

    /**
     * @param MemberLicence $memberLicence
     * @return $this
     */
    public function removeMemberLicences(MemberLicence $memberLicence): self
    {
        if ($this->member_licences->contains($memberLicence))
        {
            $this->member_licences->removeElement($memberLicence);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberManagers(): Collection
    {
        return $this->member_managers;
    }

    /**
     * @param ClubManager $clubManager
     * @return $this
     */
    public function addMemberManagers(ClubManager $clubManager): self
    {
        if (!$this->member_managers->contains($clubManager)) {
            $this->member_managers[] = $clubManager;
            $clubManager->setClubManagerMember($this);
        }

        return $this;
    }

    /**
     * @param ClubManager $clubManager
     * @return $this
     */
    public function removeMemberManagers(ClubManager $clubManager): self
    {
        if ($this->member_managers->contains($clubManager)) {
            $this->member_managers->removeElement($clubManager);
            // set the owning side to null (unless already changed)
            if ($clubManager->getClubManagerMember() === $this) {
                $clubManager->setClubManagerMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberTeachers(): Collection
    {
        return $this->member_teachers;
    }

    /**
     * @param ClubTeacher $memberTeacher
     * @return $this
     */
    public function addMemberTeachers(ClubTeacher $memberTeacher): self
    {
        if (!$this->member_teachers->contains($memberTeacher)) {
            $this->member_teachers[] = $memberTeacher;
            $memberTeacher->setClubTeacherMember($this);
        }

        return $this;
    }

    /**
     * @param ClubTeacher $memberTeacher
     * @return $this
     */
    public function removeMemberTeachers(ClubTeacher $memberTeacher): self
    {
        if ($this->member_teachers->contains($memberTeacher)) {
            $this->member_teachers->removeElement($memberTeacher);
            // set the owning side to null (unless already changed)
            if ($memberTeacher->getClubTeacherMember() === $this) {
                $memberTeacher->setClubTeacherMember(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberTitles(): Collection
    {
        return $this->member_titles;
    }

    /**
     * @param Title $title
     * @return $this
     */
    public function addMemberTitles(Title $title): self
    {
        if (!$this->member_titles->contains($title)) {
            $this->member_titles[] = $title;
            $title->setTitleMember($this);
        }

        return $this;
    }

    /**
     * @param Title $title
     * @return $this
     */
    public function removeMemberTitles(Title $title): self
    {
        if ($this->member_titles->contains($title))
        {
            $this->member_titles->removeElement($title);
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getMemberTrainingAttendances(): Collection
    {
        return $this->member_training_attendances;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
    public function addMemberTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if (!$this->member_training_attendances->contains($trainingAttendance)) {
            $this->member_training_attendances[] = $trainingAttendance;
            $trainingAttendance->setTrainingAttendanceMember($this);
        }

        return $this;
    }

    /**
     * @param TrainingAttendance $trainingAttendance
     * @return $this
     */
    public function removeMemberTrainingAttendances(TrainingAttendance $trainingAttendance): self
    {
        if ($this->member_training_attendances->contains($trainingAttendance)) {
            $this->member_training_attendances->removeElement($trainingAttendance);
            // set the owning side to null (unless already changed)
            if ($trainingAttendance->getTrainingAttendanceMember() === $this) {
                $trainingAttendance->setTrainingAttendanceMember(null);
            }
        }

        return $this;
    }

    /**
     * @return User|null
     */
    public function getMemberUser(): ?User
    {
        return $this->member_user;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function setMemberUser(User $user): self
    {
        $this->member_user = $user;

        $user->setUserMember($this);

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return string
     */
    public function getMemberSexName(): string
    {
        $listData = new ListData();

        return $listData->getSex($this->getMemberSex());
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
     * @return Grade|null
     */
    public function getMemberAikikaiDate(): ?DateTime
    {
        foreach ($this->member_grades as $grade)
        {
            if (($grade->getGradeRank() == 8) && ($grade->getGradeStatus() < 4))
            {
                return $grade->getGradeDate();
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function getMemberFreeTraining(): bool
    {
        foreach ($this->getMemberClusters() as $cluster)
        {
            if ($cluster->getCluster()->getClusterFreeTraining() && $cluster->getClusterMemberActive())
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Grade|null
     */
    public function getMemberLastExam(): ?GradeSessionCandidate
    {
        return $this->member_exams[0];
    }

    /**
     * @return Grade|null
     */
    public function getMemberLastFormation(): ?Formation
    {
        return $this->member_formations[0];
    }

    /**
     * @return Grade|null
     */
    public function getMemberLastGrade(): ?Grade
    {
        foreach ($this->member_grades as $grade)
        {
            if ($grade->getGradeStatus() < 4)
            {
                return $grade;
            }
        }

        return $this->member_grades[0];
    }

    /**
     * @return string
     */
    public function getMemberLastGradeName(): string
    {
        $listData = new ListData();

        return $listData->getGrade($this->getMemberLastGrade()->getGradeRank());
    }

    /**
     * @return DateTime|null
     */
    public function getMemberLastGradeDate(): DateTime|null
    {
        return $this->getMemberLastGrade()->getGradeDate();
    }

    /**
     * @return Grade|null
     */
    public function getMemberLastGradeAikikai(): ?Grade
    {
        foreach ($this->member_grades as $grade)
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
                return $grade;
            }
        }

        return null;
    }

    /**
     * @return Grade|null
     */
    public function getMemberLastGradeAFA(): ?Grade
    {
        foreach ($this->member_grades as $grade)
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
                return $grade;
            }
        }

        return null;
    }

    /**
     * @return MemberLicence|null
     */
    public function getMemberLastLicence(): ?MemberLicence
    {
        return $this->getMemberLicences()[0];
    }

    /**
     * @return DateTime
     */
    public function getMemberLastDeadline(): DateTime
    {
        return $this->getMemberLicences()[0]->getMemberLicenceDeadline();
    }

    /**
     * @return bool
     */
    public function getMemberNeedRenew(): bool
    {
        if ($this->getMemberLastLicence()->getMemberLicenceDeadline() > new DateTime('+3 month today'))
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
        if ($this->getMemberLastLicence()->getMemberLicenceDeadline() > new DateTime('-3 month today'))
        {
            return false;
        }

        return true;
    }

    /**
     * @return Club|null
     */
    public function getMemberActualClub(): ?Club
    {
        if (sizeof($this->getMemberLicences()) > 0)
        {
            return $this->getMemberLicences()[0]->getMemberLicenceClub();
        }

        return null;
    }

    /**
     * @return Grade|null
     */
    public function getMemberLastTitle(): ?Title
    {
        return $this->member_titles[0];
    }
}
