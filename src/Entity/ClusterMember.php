<?php
// src/Entity/ClusterMember.php
namespace App\Entity;

use App\Repository\ClusterMemberRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClusterMember
 */
#[ORM\Table(name: 'cluster_member')]
#[ORM\Entity(repositoryClass: ClusterMemberRepository::class)]
class ClusterMember
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $cluster_member_id;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cluster_member_firstname;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cluster_member_name;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $cluster_member_date_in;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $cluster_member_date_out;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $cluster_member_title;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cluster_member_email;

    /**
     * @var Cluster
     */
    #[ORM\ManyToOne(targetEntity: Cluster::class, cascade: ['persist'], inversedBy: 'cluster_members')]
    #[ORM\JoinColumn(name: 'cluster_member_join_cluster', referencedColumnName: 'cluster_id', nullable: false)]
    private Cluster $cluster;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'member_clusters')]
    #[ORM\JoinColumn(name: 'cluster_member_join_member', referencedColumnName: 'member_id', nullable: true)]
    private ?Member $cluster_member;

    /**
     * @var User|null
     */
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'user_clusters')]
    #[ORM\JoinColumn(name: 'cluster_member_join_user', referencedColumnName: 'id', nullable: true)]
    private ?User $cluster_member_user;

    /**
     * @return int
     */
    public function getClusterMemberId(): int
    {
        return $this->cluster_member_id;
    }

    /**
     * @param int $cluster_member_id
     * @return $this
     */
    public function setClusterMemberId(int $cluster_member_id): self
    {
        $this->cluster_member_id = $cluster_member_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClusterMemberFirstname(): ?string
    {
        return $this->cluster_member_firstname;
    }

    /**
     * @param string|null $cluster_member_firstname
     * @return $this
     */
    public function setClusterMemberFirstname(?string $cluster_member_firstname): self
    {
        $this->cluster_member_firstname = $cluster_member_firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClusterMemberName(): ?string
    {
        return $this->cluster_member_name;
    }

    /**
     * @param string|null $cluster_member_name
     * @return $this
     */
    public function setClusterMemberName(?string $cluster_member_name): self
    {
        $this->cluster_member_name = $cluster_member_name;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClusterMemberDateIn(): ?DateTime
    {
        return $this->cluster_member_date_in;
    }

    /**
     * @param DateTime $cluster_member_date_in
     * @return $this
     */
    public function setClusterMemberDateIn(DateTime $cluster_member_date_in): self
    {
        $this->cluster_member_date_in = $cluster_member_date_in;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getClusterMemberDateOut(): ?DateTime
    {
        return $this->cluster_member_date_out;
    }

    /**
     * @param DateTime|null $cluster_member_date_out
     * @return $this
     */
    public function setClusterMemberDateOut(?DateTime $cluster_member_date_out): self
    {
        $this->cluster_member_date_out = $cluster_member_date_out;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClusterMemberTitle(): ?int
    {
        return $this->cluster_member_title;
    }

    /**
     * @param int $clusterMemberTitle
     * @return $this
     */
    public function setClusterMemberTitle(int $clusterMemberTitle): self
    {
        $this->cluster_member_title = $clusterMemberTitle;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClusterMemberEmail(): ?string
    {
        return $this->cluster_member_email;
    }

    /**
     * @param string|null $cluster_member_email
     * @return $this
     */
    public function setClusterMemberEmail(?string $cluster_member_email): self
    {
        $this->cluster_member_email = $cluster_member_email;

        return $this;
    }

    /**
     * @return Cluster
     */
    public function getCluster(): Cluster
    {
        return $this->cluster;
    }

    /**
     * @param Cluster $cluster
     * @return $this
     */
    public function setCluster(Cluster $cluster): self
    {
        $this->cluster = $cluster;

        return $this;
    }

    /**
     * @return Member|null
     */
    public function getClusterMember(): ?Member
    {
        return $this->cluster_member;
    }

    /**
     * @param Member|null $member
     * @return $this
     */
    public function setClusterMember(?Member $member): self
    {
        $this->cluster_member = $member;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getClusterMemberUser(): ?User
    {
        return $this->cluster_member_user;
    }

    /**
     * @param User|null $user
     * @return $this
     */
    public function setClusterMemberUser(?User $user): self
    {
        $this->cluster_member_user = $user;

        return $this;
    }

    /**
     * Custom function
     */

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
}
