<?php
// src/Entity/Club.php
namespace App\Entity;

use App\Repository\ClubRepository;

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
    #[Assert\NotBlank]
    private int $clubId;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $clubName;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $clubAddress;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $clubZip;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $clubCity;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $clubProvince;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private DateTime|null $clubCreation = null;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    private int $clubType;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubBce = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubIban = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Url]
    private string|null $clubUrl = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Url]
    private string|null $clubFacebook = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Url]
    private string|null $clubInstagram = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\Url]
    private string|null $clubYoutube = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubEmail = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubPhone = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubContact = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubPresident = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubSecretary = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clubTreasurer = null;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubClassClub', targetEntity: ClubClass::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['clubClassDay' => 'ASC', 'clubClassStart' => 'ASC'])]
    private Collection|ArrayCollection|null $clubClasses;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubDojoClub', targetEntity: ClubDojo::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection|null $clubDojos;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubHistoryClub', targetEntity: ClubHistory::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['clubHistoryUpdate' => 'DESC'])]
    private Collection|ArrayCollection|null $clubHistories;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubLessonClub', targetEntity: ClubLesson::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection|ArrayCollection|null $clubLessons;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubManagerClub', targetEntity: ClubManager::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['clubManagerIsMain' => 'DESC'])]
    private Collection|ArrayCollection|null $clubManagers;

    /**
     * @var Collection|ArrayCollection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubTeacherClub', targetEntity: ClubTeacher::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['clubTeacherTitle' => 'ASC'])]
    private Collection|ArrayCollection|null $clubTeachers;

    /**
     * Club constructor.
     */
    public function __construct()
    {
        $this->clubClasses   = new ArrayCollection();
        $this->clubDojos     = new ArrayCollection();
        $this->clubHistories = new ArrayCollection();
        $this->clubLessons   = new ArrayCollection();
        $this->clubManagers  = new ArrayCollection();
        $this->clubTeachers  = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getClubId(): int
    {
        return $this->clubId;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubId(int $set): self
    {
        $this->clubId = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string
     */
    public function getClubName(bool $format = false): string
    {
        if ($format)
        {
            return $this->clubId . ' - ' . ucwords(strtolower($this->clubName));
        }

        return ucwords(strtolower($this->clubName));
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setClubName(string $set): self
    {
        $this->clubName = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @return string
     */
    public function getClubAddress(): string
    {
        return $this->clubAddress;
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setClubAddress(string $set): self
    {
        $this->clubAddress = $set;

        return $this;
    }

    /**
     * @return int
     */
    public function getClubZip(): int
    {
        return $this->clubZip;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubZip(int $set): self
    {
        $this->clubZip = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string
     */
    public function getClubCity(bool $format = false): string
    {
        return $format ? $this->clubZip . ' ' . ucwords(strtolower($this->clubCity)) : ucwords(strtolower($this->clubCity));
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setClubCity(string $set): self
    {
        $this->clubCity = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getClubProvince(bool $format = false): int|string
    {
        return $format ? $this->getClubProvinceText($this->clubProvince) : $this->clubProvince;
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getClubProvinceText(int $id = 0): array|string
    {
        $keys = array('Bruxelles' => 1, 'Brabant Wallon' => 2, 'Hainaut' => 3, 'Liège' => 4, 'Luxembourg' => 5, 'Namur' => 6, 'Brabant Flamand' => 7);

        if ($id == 0)
        {
            return $keys;
        }
        elseif ($id > sizeof($keys))
        {
            return 'Autre';
        }

        return array_search($id, $keys);
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubProvince(int $set): self
    {
        $this->clubProvince = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string|null
     */
    public function getClubCreation(bool $format = false): DateTime|string|null
    {
        if (is_null($this->clubCreation))
        {
            return $format ? 'Inconnue' : null;
        }

        return $format ? $this->clubCreation->format('d/m/Y') : $this->clubCreation;
    }

    /**
     * @param DateTime|null $set
     *
     * @return $this
     */
    public function setClubCreation(DateTime|null $set = null): self
    {
        $this->clubCreation = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string
     */
    public function getClubType(bool $format = false): int|string
    {
        return $format ? $this->getClubTypeText($this->clubType) : $this->clubType;
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getClubTypeText(int $id = 0): array|string
    {
        $keys = array('ASBL' => 1, 'Association de fait' => 2, 'Autre' => 3);

        if ($id == 0)
        {
            return $keys;
        }
        else if ($id > sizeof($keys))
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
    public function setClubType(int $set): self
    {
        $this->clubType = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getClubBce(bool $format = false): string|null
    {
        if (is_null($this->clubBce))
        {
            return $format ? 'Aucun' : null;
        }

        return $this->clubBce;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubBce(string|null $set = null): self
    {
        $this->clubBce = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getClubIban(bool $format = false): string|null
    {
        if (is_null($this->clubIban))
        {
            return $format ? 'Inconnu' : null;
        }

        return $this->clubIban;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubIban(string|null $set = null): self
    {
        $this->clubIban = $set;

        return $this;
    }

    /**
     * @param bool   $format
     * @param string $text
     *
     * @return string|null
     */
    public function getClubUrl(bool $format = false, string $text = 'Site internet'): string|null
    {
        if (is_null($this->clubUrl))
        {
            return $format ? 'Aucun site Internet' : null;
        }

        return $format ? "<a href='$this->clubUrl' target='_blank'>$text</a>" : $this->clubUrl;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubUrl(string|null $set = null): self
    {
        $this->clubUrl = $set;

        return $this;
    }

    /**
     * @param bool   $format
     * @param string $text
     *
     * @return string|null
     */
    public function getClubFacebook(bool $format = false, string $text = 'Page Facebook'): string|null
    {
        if (is_null($this->clubFacebook))
        {
            return $format ? 'Aucune page Facebook' : null;
        }

        return $format ? "<a href='$this->clubFacebook' target='_blank'>$text</a>" : $this->clubFacebook;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubFacebook(string|null $set = null): self
    {
        $this->clubFacebook = $set;

        return $this;
    }

    /**
     * @param bool   $format
     * @param string $text
     *
     * @return string|null
     */
    public function getClubInstagram(bool $format = false, string $text = 'Page Instagram'): string|null
    {
        if (is_null($this->clubInstagram))
        {
            return $format ? 'Aucune page Instagram' : null;
        }

        return $format ? "<a href='$this->clubInstagram' target='_blank'>$text</a>" : $this->clubInstagram;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubInstagram(string|null $set = null): self
    {
        $this->clubInstagram = $set;

        return $this;
    }

    /**
     * @param bool   $format
     * @param string $text
     *
     * @return string|null
     */
    public function getClubYoutube(bool $format = false, string $text = 'Page Youtube'): string|null
    {
        if (is_null($this->clubYoutube))
        {
            return $format ? 'Aucune page Youtube' : null;
        }

        return $format ? "<a href='$this->clubYoutube' target='_blank'>$text</a>" : $this->clubYoutube;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubYoutube(string|null $set = null): self
    {
        $this->clubYoutube = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return Address|string|null
     */
    public function getClubEmail(bool $format = false): Address|string|null
    {
        if (is_null($this->clubEmail))
        {
            return $format ? 'Aucune adresse Email' : null;
        }

        return $format ? new Address($this->clubEmail, $this->getClubName()) : $this->clubEmail;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubEmail(string|null $set = null): self
    {
        $this->clubEmail = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getClubPhone(bool $format = false): string|null
    {
        if (is_null($this->clubPhone))
        {
            return $format ? 'Aucun numéro de téléphone' : null;
        }

        return $this->clubPhone;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubPhone(string|null $set = null): self
    {
        $this->clubPhone = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getClubContact(bool $format = false): string|null
    {
        if (is_null($this->clubContact))
        {
            return $format ? 'Aucun nom de contact' : null;
        }

        return ucwords(strtolower($this->clubContact));
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubContact(string|null $set = null): self
    {
        $this->clubContact = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getClubPresident(bool $format = false): string|null
    {
        if (is_null($this->clubPresident))
        {
            return $format ? 'Non défini' : null;
        }

        return ucwords(strtolower($this->clubPresident));
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubPresident(string|null $set = null): self
    {
        $this->clubPresident = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getClubSecretary(bool $format = false): string|null
    {
        if (is_null($this->clubSecretary))
        {
            return $format ? 'Non défini' : null;
        }

        return ucwords(strtolower($this->clubSecretary));
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubSecretary(string|null $set = null): self
    {
        $this->clubSecretary = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getClubTreasurer(bool $format = false): string|null
    {
        if (is_null($this->clubTreasurer))
        {
            return $format ? 'Non défini' : null;
        }

        return ucwords(strtolower($this->clubTreasurer));
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setClubTreasurer(string|null $set = null): self
    {
        $this->clubTreasurer = ucwords(strtolower($set));

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClubClasses(): Collection
    {
        return $this->clubClasses;
    }

    public function getClubClassType(): string
    {
        $adult = false;
        $child = false;

        foreach ($this->clubClasses as $class)
        {
            if ($class->getClubClassType() == 3)
            {
                return $class->getClubClassTypeText(3);
            }

            if ($class->getClubClassType() == 1)
            {
                $adult = true;
            }

            if ($class->getClubClassType() == 2)
            {
                $child = true;
            }
        }

        if ($adult && $child)
        {
            return $class->getClubClassTypeText(3);
        }
        elseif ($child)
        {
            return $class->getClubClassTypeText(2);
        }

        return $class->getClubClassTypeText(1);
    }

    /**
     * @return Collection
     */
    public function getClubDojos(): Collection
    {
        return $this->clubDojos;
    }

    /**
     * @return Collection
     */
    public function getClubHistories(): Collection
    {
        return $this->clubHistories;
    }

    /**
     * @return bool
     */
    public function getClubIsActive(): bool
    {
        if ($this->clubHistories[0]->getClubHistoryStatus() == 1)
        {
            return true;
        }

        return false;
    }

    /**
     * @param bool $format
     *
     * @return array
     */
    public function getClubMembershipDate(bool $format = false): array
    {
        $histories = array('Membership' => $format ? 'En attente' : null, 'Retire' => $format ? 'Aucune' : null);

        foreach ($this->clubHistories as $history)
        {
            if ($history->getClubHistoryStatus() == 1)
            {
                $histories['Membership'] = $history->getClubHistoryUpdate($format);

                break;
            }
            elseif ($history->getClubHistoryStatus() == 3)
            {
                $histories['Retire'] = $history->getClubHistoryUpdate($format);

                break;
            }
        }

        return $histories;
    }

    /**
     * @return Collection
     */
    public function getClubLessons(): Collection
    {
        return $this->clubLessons;
    }

    /**
     * @param string|null $search
     *
     * @return Collection|ClubManager|int
     */
    public function getClubManagers(string|null $search = null): Collection|ClubManager|int
    {
        if ($search == 'Main')
        {
            return $this->clubManagers[0];
        }

        return $this->clubManagers;
    }

    /**
     * @return int
     */
    public function getClubManagersCount(): int
    {
        return sizeof($this->clubManagers);
    }

    /**
     * @param string|null $search
     *
     * @return Collection|array
     */
    public function getClubTeachers(string|null $search = null): Collection|array
    {
        if ($search == 'DojoCho')
        {
            $dojoCho = array();

            foreach ($this->clubTeachers as $teacher)
            {
                if ($teacher->getClubTeacherTitle() == 1)
                {
                    $dojoCho[] = $teacher;
                }
            }

            return $dojoCho;
        }
        elseif ($search == 'Adult')
        {
            $teachers = array();

            foreach ($this->clubTeachers as $teacher)
            {
                if ($teacher->getClubTeacherType() != 2)
                {
                    $teachers[] = $teacher;
                }
            }

            return $teachers;
        }
        elseif ($search == 'Child')
        {
            $teachers = array();

            foreach ($this->clubTeachers as $teacher)
            {
                if ($teacher->getClubTeacherType() != 1)
                {
                    $teachers[] = $teacher;
                }
            }

            return $teachers;
        }

        return $this->clubTeachers;
    }

    /**
     * @param string|null $search
     * @param bool|null   $format
     *
     * @return Address|array|string|null
     */
    public function getClubData(string|null $search = null, bool|null $format = false): Address|array|string|null
    {
        return match ($search)
        {
            'EmailDojoCho'     => $this->getEmailDojoCho($format),
            'EmailMainManager' => $this->getEmailMainManager($format),
            'EmailManagers'    => $this->getEmailManagers($format),
            'EmailStaff'       => $this->getEmailStaff($format),
            default            => null
        };
    }

    /**
     * @param bool $format
     *
     * @return array
     */
    private function getEmailDojoCho(bool $format = false): array
    {
        $email = array();

        foreach ($this->getClubTeachers('DojoCho') as $teacher)
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
     *
     * @return Address|string
     */
    private function getEmailMainManager(bool $format = false): Address|string
    {
        $manager = $this->getClubManagers('Main');

        $email = null;

        if (is_null($manager->getClubManagerMember()))
        {
            $name = $manager->getClubManagerUser()->getFirstname() . ' ' . $manager->getClubManagerUser()->getName();

            $email = $format ? new Address($manager->getClubManagerUser()->getEmail(), $name) : $manager->getClubManagerUser()->getEmail();
        }
        else
        {
            if (!is_null($manager->getClubManagerMember()->getMemberEmail()))
            {
                $name = $manager->getClubManagerMember()->getMemberFirstname() . ' ' . $manager->getClubManagerMember()->getMemberName();

                $email = $format ? new Address($manager->getClubManagerMember()->getMemberEmail(), $name) : $manager->getClubManagerMember()->getMemberEmail();
            }
        }

        if (is_null($email))
        {
            $email = $format ? new Address('afa@aikido.be', 'Secrétariat AFA') : 'afa@aikido.be';
        }

        return $email;
    }

    /**
     * @param bool $format
     *
     * @return array
     */
    private function getEmailManagers(bool $format = false): array
    {
        $email = array();

        foreach ($this->getClubManagers() as $manager)
        {
            if (is_null($manager->getClubManagerMember()))
            {
                $email[$manager->getClubManagerUser()->getEmail()] = new Address($manager->getClubManagerUser()->getEmail(), ucwords($manager->getClubManagerUser()->getFullName()));
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
     *
     * @return array
     */
    private function getEmailStaff(bool $format = false): array
    {
        $email = array_merge($this->getEmailDojoCho($format), $this->getEmailManagers($format));

        return $format ? $email : array_keys($email);
    }
}
