<?php
// src/Entity/User.php
namespace App\Entity;

use App\Repository\UserRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 */
#[ORM\Table(name: 'user')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['login'], message: 'Ce login est déjà utilisé par un autre utilisateur.', errorPath: 'login')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 25, nullable: true)]
    #[Assert\Length(min: 3, max: 20, minMessage: 'Votre login doit comporter plus de 3 caractères', maxMessage: 'Votre login doit comporter moins de 20 caractères')]
    private string $login;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $user_firstname;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $user_real_name;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $user_email;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $roles;

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private string $password;

    /**
     * @var Member|null
     */
    #[ORM\OneToOne(inversedBy: 'member_user', targetEntity: Member::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_join_member', referencedColumnName: 'member_id', nullable: true)]
    private ?Member $user_member;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'cluster_member_user', targetEntity: ClusterMember::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $user_clusters;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'club_manager_user', targetEntity: ClubManager::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $user_managers;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->user_clusters = new ArrayCollection();
        $this->user_managers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string $login
     * @return $this
     */
    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = json_decode($this->roles);
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if ($this->getUserMember() != null)
        {
            $roles[] = 'ROLE_MEMBER';
        }

        return array_unique($roles);
    }

    /**
     * @param array|null $roles
     * @return $this
     */
    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserFirstname(): ?string
    {
        return $this->user_firstname;
    }

    /**
     * @param string|null $user_firstname
     * @return $this
     */
    public function setUserFirstname(?string $user_firstname): self
    {
        $this->user_firstname = $user_firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserRealName(): ?string
    {
        return $this->user_real_name;
    }

    /**
     * @param string|null $user_real_name
     * @return $this
     */
    public function setUserRealName(?string $user_real_name): self
    {
        $this->user_real_name = $user_real_name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserEmail(): ?string
    {
        return $this->user_email;
    }

    /**
     * @param string|null $user_email
     * @return $this
     */
    public function setUserEmail(?string $user_email): self
    {
        $this->user_email = $user_email;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getUserMember(): ?Member
    {
        return $this->user_member;
    }

    /**
     * @param Member|null $user_member
     * @return $this
     */
    public function setUserMember(?Member $user_member): self
    {
        $this->user_member = $user_member;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->login;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection
     */
    public function getUserClusters(): Collection
    {
        return $this->user_clusters;
    }

    /**
     * @param ClusterMember $clusterMember
     * @return $this
     */
    public function addUserClusters(ClusterMember $clusterMember): self
    {
        if (!$this->user_clusters->contains($clusterMember)) {
            $this->user_clusters[] = $clusterMember;
            $clusterMember->setClusterMemberUser($this);
        }

        return $this;
    }

    /**
     * @param ClusterMember $clusterMember
     * @return $this
     */
    public function removeUserClusters(ClusterMember $clusterMember): self
    {
        if ($this->user_clusters->contains($clusterMember)) {
            $this->user_clusters->removeElement($clusterMember);
            // set the owning side to null (unless already changed)
            if ($clusterMember->getClusterMemberUser() === $this) {
                $clusterMember->setClusterMemberUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUserManagers(): Collection
    {
        return $this->user_managers;
    }

    /**
     * @param ClubManager $clubManager
     * @return $this
     */
    public function addUserManagers(ClubManager $clubManager): self
    {
        if (!$this->user_managers->contains($clubManager)) {
            $this->user_managers[] = $clubManager;
            $clubManager->setClubManagerUser($this);
        }

        return $this;
    }

    /**
     * @param ClubManager $clubManager
     * @return $this
     */
    public function removeUserManagers(ClubManager $clubManager): self
    {
        if ($this->user_managers->contains($clubManager)) {
            $this->user_managers->removeElement($clubManager);
            // set the owning side to null (unless already changed)
            if ($clubManager->getClubManagerUser() === $this) {
                $clubManager->setClubManagerUser(null);
            }
        }

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return array
     */
    public function getUserClubList(): array
    {
        $clubList[] = array();

        if (!is_null($this->getUserMember()?->getMemberTeachers()))
        {
            foreach ($this->getUserMember()->getMemberTeachers() as $teacher)
            {
                $clubList[] = $teacher->getClubTeacher();
            }
        }

        if (!is_null($this->getUserMember()?->getMemberManagers()))
        {
            foreach ($this->getUserMember()->getMemberManagers() as $manager)
            {
                $clubList[] = $manager->getClubManagerClub();
            }
        }

        if (!is_null($this->getUserManagers()))
        {
            foreach ($this->getUserManagers() as $manager)
            {
                $clubList[] = $manager->getClubManagerClub();
            }
        }

        return $clubList;
    }
}
