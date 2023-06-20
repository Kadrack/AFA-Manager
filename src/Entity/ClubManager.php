<?php
// src/Entity/ClubManager.php
namespace App\Entity;

use App\Repository\ClubManagerRepository;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Mime\Address;

/**
 * Class ClubManager
 */
#[ORM\Table(name: 'club_manager')]
#[ORM\Entity(repositoryClass: ClubManagerRepository::class)]
class ClubManager
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $club_manager_id;

    /**
     * @var bool|null
     */
    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $club_manager_is_main;

    /**
     * @var Club|null
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'club_managers')]
    #[ORM\JoinColumn(name: 'club_manager_join_club', referencedColumnName: 'club_id', nullable: true)]
    private ?Club $club_manager_club;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_managers')]
    #[ORM\JoinColumn(name: 'club_manager_join_member', referencedColumnName: 'member_id', nullable: true)]
    private ?Member $club_manager_member;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'user_managers')]
    #[ORM\JoinColumn(name: 'club_manager_join_user', referencedColumnName: 'id', nullable: true)]
    private ?User $club_manager_user;

    /**
     * @return int|null
     */
    public function getClubManagerId(): ?int
    {
        return $this->club_manager_id;
    }

    /**
     * @param int $club_manager_id
     * @return $this
     */
    public function setClubManagerId(int $club_manager_id): self
    {
        $this->club_manager_id = $club_manager_id;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getClubManagerIsMain(): ?bool
    {
        return $this->club_manager_is_main;
    }

    /**
     * @param bool|null $club_manager_is_main
     * @return $this
     */
    public function setClubManagerIsMain(?bool $club_manager_is_main): self
    {
        $this->club_manager_is_main = $club_manager_is_main;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getClubManagerClub(): ?Club
    {
        return $this->club_manager_club;
    }

    /**
     * @param Club|null $clubManagerClub
     * @return $this
     */
    public function setClubManagerClub(?Club $clubManagerClub): self
    {
        $this->club_manager_club = $clubManagerClub;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getClubManagerMember(): ?Member
    {
        return $this->club_manager_member;
    }

    /**
     * @param Member|null $club_manager_member
     * @return $this
     */
    public function setClubManagerMember(?Member $club_manager_member): self
    {
        $this->club_manager_member = $club_manager_member;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getClubManagerUser(): ?User
    {
        return $this->club_manager_user;
    }

    /**
     * @param User|null $club_manager_user
     * @return $this
     */
    public function setClubManagerUser(?User $club_manager_user): self
    {
        $this->club_manager_user = $club_manager_user;

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return string
     */
    public function getClubManagerLogin(): string
    {
        if (is_null($this->club_manager_member))
        {
            return $this->club_manager_user->getLogin();
        }
        else
        {
            $login = $this->club_manager_member->getMemberUser()?->getLogin();

            if (is_null($login))
            {
                return 'Aucun';
            }
            else
            {
                return $login;
            }
        }
    }

    /**
     * @return int|string
     */
    public function getClubManagerMemberId(): int|string
    {
        if (is_null($this->club_manager_member))
        {
            return 'Aucun';
        }
        else
        {
            return $this->club_manager_member->getMemberId();
        }
    }

    /**
     * @return string
     */
    public function getClubManagerFirstname(): string
    {
        if (is_null($this->club_manager_member))
        {
            return $this->club_manager_user->getUserFirstname();
        }
        else
        {
            return $this->club_manager_member->getMemberFirstname();
        }
    }

    /**
     * @return string
     */
    public function getClubManagerName(): string
    {
        if (is_null($this->club_manager_member))
        {
            return $this->club_manager_user->getUserName();
        }
        else
        {
            return $this->club_manager_member->getMemberName();
        }
    }

    /**
     * @param bool $format
     * @return string|Address|null
     */
    public function getClubManagerEmail(bool $format = false): string|Address|null
    {
        $email = null;

        if (is_null($this->club_manager_member))
        {
            if ($format)
            {
                $email = new Address($this->club_manager_user->getUserEmail(), ucwords($this->club_manager_user->getUserFirstname()) . ' ' . ucwords($this->club_manager_user->getUserRealName()));
            }
            else
            {
                $email = $this->club_manager_user->getUserEmail();
            }
        }
        else
        {
            if (!is_null($this->club_manager_member->getMemberEmail()))
            {
                if ($format)
                {
                    $email = new Address($this->club_manager_member->getMemberEmail(), ucwords($this->club_manager_member->getMemberFirstname()) . ' ' . ucwords($this->club_manager_member->getMemberName()));
                }
                else
                {
                    $email = $this->club_manager_member->getMemberEmail();
                }
            }
        }

        return $email;
    }
}
