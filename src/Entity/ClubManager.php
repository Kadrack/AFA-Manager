<?php
// src/Entity/ClubManager.php
namespace App\Entity;

use App\Repository\ClubManagerRepository;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Mime\Address;

/**
 * Class ClubManager
 */
#[ORM\Table(name: 'clubManager')]
#[ORM\Entity(repositoryClass: ClubManagerRepository::class)]
class ClubManager
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $clubManagerId;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean')]
    private bool $clubManagerIsMain = false;

    /**
     * @var Club
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'clubManagers')]
    #[ORM\JoinColumn(name: 'clubManager_join_club', referencedColumnName: 'clubId')]
    private Club $clubManagerClub;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberClubManagers')]
    #[ORM\JoinColumn(name: 'clubManager_join_member', referencedColumnName: 'memberId', nullable: true)]
    private Member|null $clubManagerMember = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'managers')]
    #[ORM\JoinColumn(name: 'clubManager_join_user', referencedColumnName: 'id', nullable: true)]
    private User|null $clubManagerUser = null;

    /**
     * @return int|null
     */
    public function getClubManagerId(): ?int
    {
        return $this->clubManagerId;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubManagerId(int $set): self
    {
        $this->clubManagerId = $set;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClubManagerIsMain(): bool
    {
        return $this->clubManagerIsMain;
    }

    /**
     * @param bool $set
     *
     * @return $this
     */
    public function setClubManagerIsMain(bool $set = false): self
    {
        $this->clubManagerIsMain = $set;

        return $this;
    }

    /**
     * @return Club
     */
    public function getClubManagerClub(): Club
    {
        return $this->clubManagerClub;
    }

    /**
     * @param Club $set
     *
     * @return $this
     */
    public function setClubManagerClub(Club $set): self
    {
        $this->clubManagerClub = $set;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getClubManagerMember(): Member|null
    {
        return $this->clubManagerMember;
    }

    /**
     * @param Member|null $set
     *
     * @return $this
     */
    public function setClubManagerMember(Member|null $set = null): self
    {
        $this->clubManagerMember = $set;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getClubManagerUser(): User|null
    {
        return $this->clubManagerUser;
    }

    /**
     * @param User|null $set
     *
     * @return $this
     */
    public function setClubManagerUser(User|null $set = null): self
    {
        $this->clubManagerUser = $set;

        return $this;
    }

    /**
     * @return string
     */
    public function getClubManagerLogin(): string
    {
        if (is_null($this->clubManagerMember))
        {
            return $this->clubManagerUser->getLogin();
        }

        $login = $this->clubManagerMember->getMemberUser()?->getLogin();

        return is_null($login) ? 'Aucun' : $login;
    }

    /**
     * @return int|string
     */
    public function getClubManagerMemberId(): int|string
    {
        if (is_null($this->clubManagerMember))
        {
            return 'Aucun';
        }

        return $this->clubManagerMember->getMemberId();
    }

    /**
     * @return string
     */
    public function getClubManagerFullName(): string
    {
        if (is_null($this->clubManagerMember))
        {
            return $this->clubManagerUser->getFullName();
        }

        return $this->clubManagerMember->getMemberFullName();
    }

    /**
     * @return string
     */
    public function getClubManagerFirstname(): string
    {
        if (is_null($this->clubManagerMember))
        {
            return $this->clubManagerUser->getFirstname();
        }

        return $this->clubManagerMember->getMemberFirstname();
    }

    /**
     * @return string
     */
    public function getClubManagerName(): string
    {
        if (is_null($this->clubManagerMember))
        {
            return $this->clubManagerUser->getName();
        }

        return $this->clubManagerMember->getMemberName();
    }

    /**
     * @param bool $format
     *
     * @return Address|string|null
     */
    public function getClubManagerEmail(bool $format = false): Address|string|null
    {
        $email = null;

        if (is_null($this->clubManagerMember))
        {
            if ($format)
            {
                $email = new Address($this->clubManagerUser->getEmail(), ucwords($this->clubManagerUser->getFirstname()) . ' ' . ucwords($this->clubManagerUser->getName()));
            }
            else
            {
                $email = $this->clubManagerUser->getEmail();
            }
        }
        else
        {
            if (!is_null($this->clubManagerMember->getMemberEmail()))
            {
                if ($format)
                {
                    $email = new Address($this->clubManagerMember->getMemberEmail(), ucwords($this->clubManagerMember->getMemberFirstname()) . ' ' . ucwords($this->clubManagerMember->getMemberName()));
                }
                else
                {
                    $email = $this->clubManagerMember->getMemberEmail();
                }
            }
        }

        return $email;
    }
}
