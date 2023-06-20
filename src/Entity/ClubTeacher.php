<?php
// src/Entity/ClubTeacher.php
namespace App\Entity;

use App\Repository\ClubTeacherRepository;

use App\Service\ListData;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Mime\Address;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ClubTeacher
 */
#[ORM\Table(name: 'club_teacher')]
#[ORM\Entity(repositoryClass: ClubTeacherRepository::class)]
class ClubTeacher
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $club_teacher_id;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_teacher_firstname;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $club_teacher_name;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $club_teacher_grade;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $club_teacher_title_aikikai;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $club_teacher_title_adeps;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $club_teacher_title;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $club_teacher_type;

    /**
     * @var Club
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'club_teachers')]
    #[ORM\JoinColumn(name: 'club_teacher_join_club', referencedColumnName: 'club_id', nullable: false)]
    private Club $club_teacher;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_teachers')]
    #[ORM\JoinColumn(name: 'club_teacher_join_member', referencedColumnName: 'member_id', nullable: true)]
    private ?Member $club_teacher_member;

    /**
     * @return int|null
     */
    public function getClubTeacherId(): ?int
    {
        return $this->club_teacher_id;
    }

    /**
     * @param int $club_teacher_id
     * @return $this
     */
    public function setClubTeacherId(int $club_teacher_id): self
    {
        $this->club_teacher_id = $club_teacher_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubTeacherFirstname(): ?string
    {
        return $this->club_teacher_firstname;
    }

    /**
     * @param string|null $club_teacher_firstname
     * @return $this
     */
    public function setClubTeacherFirstname(?string $club_teacher_firstname): self
    {
        $this->club_teacher_firstname = $club_teacher_firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubTeacherName(): ?string
    {
        return $this->club_teacher_name;
    }

    /**
     * @param string|null $club_teacher_name
     * @return $this
     */
    public function setClubTeacherName(?string $club_teacher_name): self
    {
        $this->club_teacher_name = $club_teacher_name;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubTeacherGrade(): ?int
    {
        return $this->club_teacher_grade;
    }

    /**
     * @param int|null $club_teacher_grade
     * @return $this
     */
    public function setClubTeacherGrade(?int $club_teacher_grade): self
    {
        $this->club_teacher_grade = $club_teacher_grade;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubTeacherTitleAikikai(): ?int
    {
        return $this->club_teacher_title_aikikai;
    }

    /**
     * @param int|null $club_teacher_title_aikikai
     * @return $this
     */
    public function setClubTeacherTitleAikikai(?int $club_teacher_title_aikikai): self
    {
        $this->club_teacher_title_aikikai = $club_teacher_title_aikikai;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubTeacherTitleAdeps(): ?int
    {
        return $this->club_teacher_title_adeps;
    }

    /**
     * @param int|null $club_teacher_title_adeps
     * @return $this
     */
    public function setClubTeacherTitleAdeps(?int $club_teacher_title_adeps): self
    {
        $this->club_teacher_title_adeps = $club_teacher_title_adeps;

        return $this;
    }

    /**
     * @return int
     */
    public function getClubTeacherTitle(): int
    {
        return $this->club_teacher_title;
    }

    /**
     * @param int $club_teacher_title
     * @return $this
     */
    public function setClubTeacherTitle(int $club_teacher_title): self
    {
        $this->club_teacher_title = $club_teacher_title;

        return $this;
    }

    /**
     * @return int
     */
    public function getClubTeacherType(): int
    {
        return $this->club_teacher_type;
    }

    /**
     * @param int $club_teacher_type
     * @return $this
     */
    public function setClubTeacherType(int $club_teacher_type): self
    {
        $this->club_teacher_type = $club_teacher_type;

        return $this;
    }

    /**
     * @return Club
     */
    public function getClubTeacher(): Club
    {
        return $this->club_teacher;
    }

    /**
     * @param Club $club_teacher
     * @return $this
     */
    public function setClubTeacher(Club $club_teacher): self
    {
        $this->club_teacher = $club_teacher;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getClubTeacherMember(): ?Member
    {
        return $this->club_teacher_member;
    }

    /**
     * @param Member|null $club_teacher_member
     * @return $this
     */
    public function setClubTeacherMember(?Member $club_teacher_member): self
    {
        $this->club_teacher_member = $club_teacher_member;

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @param bool $format
     * @return string|Address|null
     */
    public function getClubTeacherEmail(bool $format = false): string|Address|null
    {
        if (!is_null($this->getClubTeacherMember()))
        {
            if ($format)
            {
                $email = new Address($this->club_teacher_member->getMemberEmail(), ucwords($this->club_teacher_member->getMemberFirstname()) . ' ' . ucwords($this->club_teacher_member->getMemberName()));
            }
            else
            {
                $email = $this->club_teacher_member->getMemberEmail();
            }

            return $email;
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getClubTeacherFirstnameDisplay(): ?string
    {
        if (is_null($this->getClubTeacherFirstname()))
        {
            return $this->club_teacher_member?->getMemberFirstname();
        }
        else
        {
            return $this->club_teacher_firstname;
        }
    }

    /**
     * @return string|null
     */
    public function getClubTeacherNameDisplay(): ?string
    {
        if (is_null($this->getClubTeacherName()))
        {
            return $this->club_teacher_member?->getMemberName();
        }
        else
        {
            return $this->club_teacher_name;
        }
    }

    /**
     * @return string
     */
    public function getClubTeacherTitleDisplay(): string
    {
        $listData = new ListData();

        return $listData->getTeacherTitle($this->club_teacher_title);
    }

    /**
     * @return string
     */
    public function getClubTeacherTypeDisplay(): string
    {
        $listData = new ListData();

        return $listData->getTeacherType($this->club_teacher_type);
    }

    /**
     * @return string
     */
    public function getClubTeacherGradeDisplay(): string
    {
        $listData = new ListData();

        if (!is_null($this->getClubTeacherMember()))
        {
            return $listData->getGrade($this->club_teacher_member->getMemberLastGrade()->getGradeRank());
        }
        elseif (!is_null($this->getClubTeacherGrade()))
        {
            return $listData->getGrade($this->club_teacher_grade);
        }
        else
        {
            return 'Aucun';
        }
    }

    /**
     * @return string
     */
    public function getClubTeacherTitleAikikaiDisplay(): string
    {
        $listData = new ListData();

        if (!is_null($this->club_teacher_member?->getMemberLastTitle()?->getTitleRank()))
        {
            return $listData->getTitleAikikai($this->club_teacher_member->getMemberLastTitle()->getTitleRank());
        }
        elseif (!is_null($this->club_teacher_title_aikikai))
        {
            return $listData->getTitleAikikai($this->club_teacher_title_aikikai);
        }
        else
        {
            return 'Aucun';
        }
    }

    /**
     * @return string
     */
    public function getClubTeacherTitleAdepsDisplay(): string
    {
        $listData = new ListData();

        if (!is_null($this->club_teacher_member?->getMemberLastFormation()?->getFormationRank()))
        {
            return $listData->getFormation($this->club_teacher_member->getMemberLastFormation()->getFormationRank());
        }
        elseif (!is_null($this->club_teacher_title_adeps))
        {
            return $listData->getFormation($this->club_teacher_title_adeps);
        }
        else
        {
            return 'Aucun';
        }
    }
}
