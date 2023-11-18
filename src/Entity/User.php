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
    private ?string $firstname;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email;

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
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $theme;

    /**
     * @var Member|null
     */
    #[ORM\OneToOne(inversedBy: 'memberUser', targetEntity: Member::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_join_member', referencedColumnName: 'memberId', nullable: true)]
    private ?Member $member;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'clusterMemberUser', targetEntity: ClusterMember::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $clusters;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'clubManagerUser', targetEntity: ClubManager::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $managers;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->clusters = new ArrayCollection();
        $this->managers = new ArrayCollection();
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
        if (!is_null($this->roles))
        {
            $roles = json_decode($this->roles);
        }

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        if ($this->getMember() != null)
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
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    /**
     * @param string|null $theme
     *
     * @return $this
     */
    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     *
     * @return $this
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullName(): ?string
    {
        return $this->firstname . ' ' . $this->name;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return $this
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getMember(): ?Member
    {
        return $this->member;
    }

    /**
     * @param Member|null $member
     *
     * @return $this
     */
    public function setMember(?Member $member): self
    {
        $this->member = $member;

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
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection
     */
    public function getClusters(): Collection
    {
        return $this->clusters;
    }

    /**
     * @param ClusterMember $clusterMember
     * @return $this
     */
    public function addUserClusters(ClusterMember $clusterMember): self
    {
        if (!$this->clusters->contains($clusterMember)) {
            $this->clusters[] = $clusterMember;
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
        if ($this->clusters->contains($clusterMember)) {
            $this->clusters->removeElement($clusterMember);
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
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    /**
     * @param ClubManager $clubManager
     * @return $this
     */
    public function addUserManagers(ClubManager $clubManager): self
    {
        if (!$this->managers->contains($clubManager)) {
            $this->managers[] = $clubManager;
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
        if ($this->managers->contains($clubManager)) {
            $this->managers->removeElement($clubManager);
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

        if (!is_null($this->getMember()?->getMemberClubTeachers()))
        {
            foreach ($this->getMember()->getMemberClubTeachers() as $teacher)
            {
                $clubList[] = $teacher->getClubTeacherClub();
            }
        }

        if (!is_null($this->getMember()?->getMemberClubManagers()))
        {
            foreach ($this->getMember()->getMemberClubManagers() as $manager)
            {
                $clubList[] = $manager->getClubManagerClub();
            }
        }

        if (!is_null($this->getManagers()))
        {
            foreach ($this->getManagers() as $manager)
            {
                $clubList[] = $manager->getClubManagerClub();
            }
        }

        return $clubList;
    }
}
