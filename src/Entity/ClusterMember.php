<?php
// src/Entity/ClusterMember.php
namespace App\Entity;

use App\Repository\ClusterMemberRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClusterMember
 */
#[ORM\Table(name: 'clusterMember')]
#[ORM\Entity(repositoryClass: ClusterMemberRepository::class)]
class ClusterMember
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $clusterMemberId;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clusterMemberFirstname = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clusterMemberName = null;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $clusterMemberDateIn;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private DateTime|null $clusterMemberDateOut = null;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private int|null $clusterMemberTitle = null;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private string|null $clusterMemberEmail = null;

    /**
     * @var Cluster
     */
    #[ORM\ManyToOne(targetEntity: Cluster::class, cascade: ['persist'], inversedBy: 'clusterMembers')]
    #[ORM\JoinColumn(name: 'clusterMember_join_cluster', referencedColumnName: 'clusterId', nullable: false)]
    private Cluster $clusterMemberCluster;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberClusterMembers')]
    #[ORM\JoinColumn(name: 'clusterMember_join_member', referencedColumnName: 'memberId', nullable: true)]
    private Member|null $clusterMember = null;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'clusters')]
    #[ORM\JoinColumn(name: 'clusterMember_join_user', referencedColumnName: 'id', nullable: true)]
    private User|null $clusterMemberUser = null;

    /**
     * @return int
     */
    public function getClusterMemberId(): int
    {
        return $this->clusterMemberId;
    }

    /**
     * @param int $clusterMemberId
     *
     * @return $this
     */
    public function setClusterMemberId(int $clusterMemberId): self
    {
        $this->clusterMemberId = $clusterMemberId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClusterMemberFirstname(): string|null
    {
        if (!is_null($this->clusterMemberFirstname))
        {
            return ucwords(strtolower($this->clusterMemberFirstname));
        }
        elseif (!is_null($this->clusterMember))
        {
            return $this->clusterMember->getMemberFirstname();
        }
        elseif (!is_null($this->getClusterMemberUser()))
        {
            return $this->getClusterMemberUser()->getFirstname();
        }

        return 'Inconnu';
    }

    /**
     * @param string|null $clusterMemberFirstname
     *
     * @return $this
     */
    public function setClusterMemberFirstname(string|null $clusterMemberFirstname = null): self
    {
        $this->clusterMemberFirstname = $clusterMemberFirstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClusterMemberName(): string|null
    {
        if (!is_null($this->clusterMemberName))
        {
            return ucwords(strtolower($this->clusterMemberName));
        }
        elseif (!is_null($this->clusterMember))
        {
            return $this->clusterMember->getMemberName();
        }
        elseif (!is_null($this->getClusterMemberUser()))
        {
            return $this->getClusterMemberUser()->getName();
        }

        return 'Inconnu';
    }

    /**
     * @param string|null $clusterMemberName
     *
     * @return $this
     */
    public function setClusterMemberName(string|null $clusterMemberName = null): self
    {
        $this->clusterMemberName = $clusterMemberName;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClusterMemberDateIn(): DateTime|null
    {
        return $this->clusterMemberDateIn;
    }

    /**
     * @return bool
     */
    public function getClusterMemberFuture(): bool
    {
        if ($this->getClusterMemberDateIn() > new DateTime())
        {
            return true;
        }

        return false;
    }

    /**
     * @param DateTime $clusterMemberDateIn
     *
     * @return $this
     */
    public function setClusterMemberDateIn(DateTime $clusterMemberDateIn): self
    {
        $this->clusterMemberDateIn = $clusterMemberDateIn;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClusterMemberDateOut(): DateTime|null
    {
        return $this->clusterMemberDateOut;
    }

    /**
     * @return bool
     */
    public function getClusterMemberOld(): bool
    {
        if (!is_null($this->getClusterMemberDateOut()) && $this->getClusterMemberDateOut() < new DateTime())
        {
            return true;
        }

        return false;
    }

    /**
     * @param DateTime|null $clusterMemberDateOut
     *
     * @return $this
     */
    public function setClusterMemberDateOut(DateTime|null $clusterMemberDateOut = null): self
    {
        $this->clusterMemberDateOut = $clusterMemberDateOut;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClusterMemberActive(): bool
    {
        if (is_null($this->getClusterMemberDateOut()) && ($this->getClusterMemberDateIn() <= new DateTime()))
        {
            return true;
        }

        if (($this->getClusterMemberDateIn() <= new DateTime()) && $this->getClusterMemberDateOut() >= new DateTime())
        {
            return true;
        }

        return false;
    }

    /**
     * @param bool|null $format
     * @param bool|null $list
     *
     * @return int|string|null
     */
    public function getClusterMemberTitle(bool|null $format = false, bool|null $list = false): int|string|null
    {
        if ($format && $list)
        {
            return $this->getMemberTitle(0);
        }
        elseif ($format && is_null($this->clusterMemberTitle))
        {
            return 'Aucun';
        }
        elseif ($format)
        {
            return $this->getMemberTitle($this->clusterMemberTitle);
        }

        return $this->clusterMemberTitle;
    }

    private function getMemberTitle(int $title): array|string
    {
        $titles = array('Président(e)' => 1, 'Vice-Président(e)' => 2, 'Secrétaire général(e)' => 3, 'Trésorier(ère) général(e)' => 4, 'Délégué(e) technique' => 5, 'Délégué(e) au relations interfédérales' => 6, 'Responsable communication' => 7, 'Community manager' => 8, 'Administrateur(trice)' => 9, 'Secrétaire' => 10, 'Membre' => 11, 'Procureur' => 12, 'Juge' => 13, 'Relais' => 14);

        if ($title == 0)
        {
            return $titles;
        }
        else if ($title > sizeof($titles))
        {
            return "Autre";
        }
        else
        {
            return array_search($title, $titles);
        }
    }

    /**
     * @param int $clusterMemberTitle
     *
     * @return $this
     */
    public function setClusterMemberTitle(int $clusterMemberTitle): self
    {
        $this->clusterMemberTitle = $clusterMemberTitle;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClusterMemberEmail(): string|null
    {
        return $this->clusterMemberEmail;
    }

    /**
     * @param string|null $clusterMemberEmail
     *
     * @return $this
     */
    public function setClusterMemberEmail(string|null $clusterMemberEmail = null): self
    {
        $this->clusterMemberEmail = $clusterMemberEmail;

        return $this;
    }

    /**
     * @return Cluster
     */
    public function getClusterMemberCluster(): Cluster
    {
        return $this->clusterMemberCluster;
    }

    /**
     * @param Cluster $clusterMemberCluster
     *
     * @return $this
     */
    public function setClusterMemberCluster(Cluster $clusterMemberCluster): self
    {
        $this->clusterMemberCluster = $clusterMemberCluster;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getClusterMember(): Member|null
    {
        return $this->clusterMember;
    }

    /**
     * @param Member|null $member
     * @return $this
     */
    public function setClusterMember(Member|null $member = null): self
    {
        $this->clusterMember = $member;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getClusterMemberUser(): User|null
    {
        return $this->clusterMemberUser;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setClusterMemberUser(User|null $user = null): self
    {
        $this->clusterMemberUser = $user;

        return $this;
    }

    /**
     * @param string|null $search
     *
     * @return string|null
     */
    public function getClusterMemberData(?string $search): string|null
    {
        if ($search == 'Phone')
        {
            if (!is_null($this->clusterMember))
            {
                return $this->clusterMember->getMemberPhone();
            }
            else
            {
                return 'Inconnu';
            }
        }
        elseif ($search == 'Email')
        {
            if (!is_null($this->clusterMember))
            {
                return $this->clusterMember->getMemberEmail();
            }
            elseif (!is_null($this->getClusterMemberUser()))
            {
                return $this->getClusterMemberUser()->getEmail();
            }
            else
            {
                return 'Inconnue';
            }
        }

        return null;
    }
}
