<?php
// src/Entity/ClubTeacher.php
namespace App\Entity;

use App\Repository\ClubTeacherRepository;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Mime\Address;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ClubTeacher
 */
#[ORM\Table(name: 'clubTeacher')]
#[ORM\Entity(repositoryClass: ClubTeacherRepository::class)]
class ClubTeacher
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $clubTeacherId;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubTeacherFirstname = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubTeacherName = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int|null $clubTeacherGrade = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int|null $clubTeacherTitleAikikai = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int|null $clubTeacherTitleAdeps = null;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $clubTeacherTitle;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $clubTeacherType;

    /**
     * @var Club
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'clubTeachers')]
    #[ORM\JoinColumn(name: 'clubTeacher_join_club', referencedColumnName: 'clubId', nullable: false)]
    private Club $clubTeacherClub;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberClubTeachers')]
    #[ORM\JoinColumn(name: 'clubTeacher_join_member', referencedColumnName: 'memberId', nullable: true)]
    private Member|null $clubTeacherMember = null;

    /**
     * @return int
     */
    public function getClubTeacherId(): int
    {
        return $this->clubTeacherId;
    }

    /**
     * @param int $clubTeacherId
     *
     * @return $this
     */
    public function setClubTeacherId(int $clubTeacherId): self
    {
        $this->clubTeacherId = $clubTeacherId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubTeacherFirstname(): string|null
    {
        if (is_null($this->clubTeacherFirstname))
        {
            return ucwords(strtolower($this->clubTeacherMember?->getMemberFirstname()));
        }

        return ucwords(strtolower($this->clubTeacherFirstname));
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubTeacherFirstname(string|null $set = null): self
    {
        $this->clubTeacherFirstname = is_null($set) ? null : ucwords(strtolower($set));

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClubTeacherName(): string|null
    {
        if (is_null($this->clubTeacherName))
        {
            return ucwords(strtolower($this->clubTeacherMember?->getMemberName()));
        }

        return ucwords(strtolower($this->clubTeacherName));
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubTeacherName(string|null $set = null): self
    {
        $this->clubTeacherName = is_null($set) ? null : ucwords(strtolower($set));

        return $this;
    }

    /**
     * @param bool|null $format
     *
     * @return int|string|null
     */
    public function getClubTeacherGrade(bool $format = false): int|string|null
    {
        if ($format)
        {
            if (is_null($this->clubTeacherMember))
            {
                if (is_null($this->clubTeacherGrade))
                {
                    return 'Inconnu';
                }

                return $this->getClubTeacherGradeText($this->clubTeacherGrade);
            }

            return $this->getClubTeacherGradeText($this->clubTeacherMember->getMemberLastGrade()->getGradeRank());
        }
        else
        {
            return $this->clubTeacherGrade;
        }
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getClubTeacherGradeText(int $id = 0): array|string
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
        else
        {
            return array_search($id, $keys);
        }
    }

    /**
     * @param int|null $set
     *
     * @return $this
     */
    public function setClubTeacherGrade(int|null $set = null): self
    {
        $this->clubTeacherGrade = $set;

        return $this;
    }

    /**
     * @param bool $format
     * @param bool $null
     *
     * @return int|string|null
     */
    public function getClubTeacherTitleAikikai(bool $format = false, bool $null = false): int|string|null
    {
        if ($format)
        {
            if (is_null($this->clubTeacherMember))
            {
                if (is_null($this->clubTeacherTitleAikikai))
                {
                    return $null ? null : 'Aucun';
                }
                else
                {
                    return $this->getClubTeacherTitleAikikaiText($this->clubTeacherTitleAikikai);
                }
            }
            else
            {
                if (is_null($this->clubTeacherMember->getMemberLastTitle()?->getTitleRank()))
                {
                    return $null ? null : 'Aucun';
                }
                else
                {
                    return $this->getClubTeacherTitleAikikaiText($this->clubTeacherMember->getMemberLastTitle()->getTitleRank());
                }
            }
        }
        else
        {
            return $this->clubTeacherTitleAikikai;
        }
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getClubTeacherTitleAikikaiText(int $id = 0): array|string
    {
        $keys = array('Fuku Shidoïn' => 1, 'Shidoïn' => 2, 'Shihan' => 3);

        if ($id == 0)
        {
            return $keys;
        }
        else if ($id > sizeof($keys))
        {
            return 'Autre';
        }
        else
        {
            return array_search($id, $keys);
        }
    }

    /**
     * @param int|null $set
     *
     * @return $this
     */
    public function setClubTeacherTitleAikikai(int|null $set = null): self
    {
        $this->clubTeacherTitleAikikai = $set;

        return $this;
    }

    /**
     * @param bool $format
     * @param bool $null
     *
     * @return int|string|null
     */
    public function getClubTeacherTitleAdeps(bool $format = false, bool $null = false): int|string|null
    {
        if ($format)
        {
            if (is_null($this->clubTeacherMember))
            {
                if (is_null($this->clubTeacherTitleAdeps))
                {
                    return $null ? null : 'Aucun';
                }
                else
                {
                    return $this->getClubTeacherTitleAdepsText($this->clubTeacherTitleAdeps);
                }
            }
            else
            {
                if (is_null($this->clubTeacherMember->getMemberLastFormation()?->getFormationRank()))
                {
                    return $null ? null : 'Aucun';
                }
                else
                {
                    return $this->getClubTeacherTitleAdepsText($this->clubTeacherMember->getMemberLastFormation()->getFormationRank());
                }
            }
        }
        else
        {
            return $this->clubTeacherTitleAdeps;
        }
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getClubTeacherTitleAdepsText(int $id = 0): array|string
    {
        $keys = array('Initiateur' => 1, 'Aide-Moniteur' => 2, 'Moniteur' => 3, 'Moniteur Animateur' => 4, 'Moniteur Initiateur' => 5, 'Moniteur Educateur' => 6);

        if ($id == 0)
        {
            return $keys;
        }
        else if ($id > sizeof($keys))
        {
            return 'Autre';
        }
        else
        {
            return array_search($id, $keys);
        }
    }

    /**
     * @param int|null $set
     *
     * @return $this
     */
    public function setClubTeacherTitleAdeps(int|null $set = null): self
    {
        $this->clubTeacherTitleAdeps = $set;

        return $this;
    }

    /**
     * @param bool|null $format
     *
     * @return int|string
     */
    public function getClubTeacherTitle(bool $format = false): int|string
    {
        if ($format)
        {
            return $this->getClubTeacherTitleText($this->clubTeacherTitle);
        }
        else
        {
            return $this->clubTeacherTitle;
        }
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getClubTeacherTitleText(int $id = 0): array|string
    {
        $keys = array('Dojo Cho' => 1, 'Professeur' => 2, 'Assistant' => 3);

        if ($id == 0)
        {
            return $keys;
        }
        else if ($id > sizeof($keys))
        {
            return 'Autre';
        }
        else
        {
            return array_search($id, $keys);
        }
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubTeacherTitle(int $set): self
    {
        $this->clubTeacherTitle = $set;

        return $this;
    }

    /**
     * @param bool|null $format
     *
     * @return int|string
     */
    public function getClubTeacherType(bool $format = false): int|string
    {
        if ($format)
        {
            return $this->getClubTeacherTypeText($this->clubTeacherType);
        }
        else
        {
            return $this->clubTeacherType;
        }
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getClubTeacherTypeText(int $id = 0): array|string
    {
        $keys = array('Adultes' => 1, 'Enfants' => 2, 'Adultes/Enfants' => 3);

        if ($id == 0)
        {
            return $keys;
        }
        else if ($id > sizeof($keys))
        {
            return 'Autre';
        }
        else
        {
            return array_search($id, $keys);
        }
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubTeacherType(int $set): self
    {
        $this->clubTeacherType = $set;

        return $this;
    }

    /**
     * @return Club
     */
    public function getClubTeacherClub(): Club
    {
        return $this->clubTeacherClub;
    }

    /**
     * @param Club $set
     *
     * @return $this
     */
    public function setClubTeacherClub(Club $set): self
    {
        $this->clubTeacherClub = $set;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getClubTeacherMember(): Member|null
    {
        return $this->clubTeacherMember;
    }

    /**
     * @param Member|null $clubTeacherMember
     *
     * @return $this
     */
    public function setClubTeacherMember(Member|null $clubTeacherMember = null): self
    {
        $this->clubTeacherMember = $clubTeacherMember;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return Address|string|null
     */
    public function getClubTeacherEmail(bool $format = false): Address|string|null
    {
        if (!is_null($this->getClubTeacherMember()))
        {
            if ($format)
            {
                return new Address($this->clubTeacherMember->getMemberEmail(), ucwords($this->clubTeacherMember->getMemberFirstname()) . ' ' . ucwords($this->clubTeacherMember->getMemberName()));
            }
            else
            {
                return $this->clubTeacherMember->getMemberEmail();
            }
        }
        else
        {
            return null;
        }
    }
}
