<?php
// src/Entity/Cluster.php
namespace App\Entity;

use App\Repository\ClusterRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Cluster
 */
#[ORM\Table(name: 'cluster')]
#[ORM\Entity(repositoryClass: ClusterRepository::class)]
class Cluster
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $cluster_id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $cluster_name;

    /**
     * @var int
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private int $cluster_free_training = 0;

    /**
     * @var int
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private int $cluster_use_title = 0;

    /**
     * @var int
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private int $cluster_use_email = 0;

    /**
     * @var int
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private int $cluster_give_access = 0;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'cluster', targetEntity: ClusterMember::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['cluster_member_date_in' => 'DESC', 'cluster_member_title' => 'ASC'])]
    private ArrayCollection|Collection|null $cluster_members;

    /**
     * Cluster constructor.
     */
    public function __construct()
    {
        $this->cluster_members = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getClusterId(): int
    {
        return $this->cluster_id;
    }

    /**
     * @param int $cluster_id
     * @return $this
     */
    public function setClusterId(int $cluster_id): self
    {
        $this->cluster_id = $cluster_id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getClusterName(): ?string
    {
        return $this->cluster_name;
    }

    /**
     * @param string $cluster_name
     * @return $this
     */
    public function setClusterName(string $cluster_name): self
    {
        $this->cluster_name = $cluster_name;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClusterFreeTraining(): bool
    {
        return $this->cluster_free_training;
    }

    /**
     * @param bool $cluster_free_training
     * @return $this
     */
    public function setClusterFreeTraining(bool $cluster_free_training): self
    {
        $this->cluster_free_training = $cluster_free_training;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClusterUseTitle(): bool
    {
        return $this->cluster_use_title;
    }

    /**
     * @param bool $cluster_use_title
     * @return $this
     */
    public function setClusterUseTitle(bool $cluster_use_title): self
    {
        $this->cluster_use_title = $cluster_use_title;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClusterUseEmail(): bool
    {
        return $this->cluster_use_email;
    }

    /**
     * @param bool $cluster_use_email
     * @return $this
     */
    public function setClusterUseEmail(bool $cluster_use_email): self
    {
        $this->cluster_use_email = $cluster_use_email;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClusterGiveAccess(): bool
    {
        return $this->cluster_give_access;
    }

    /**
     * @param bool $cluster_give_access
     * @return $this
     */
    public function setClusterGiveAccess(bool $cluster_give_access): self
    {
        $this->cluster_give_access = $cluster_give_access;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClusterMembers(): Collection
    {
        return $this->cluster_members;
    }

    /**
     * @param ClusterMember $clusterMember
     * @return $this
     */
    public function addClusterMembers(ClusterMember $clusterMember): self
    {
        if (!$this->cluster_members->contains($clusterMember)) {
            $this->cluster_members[] = $clusterMember;
            $clusterMember->setCluster($this);
        }

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return array
     */
    public function getClusterFutureMembers(): array
    {
        $future = array();

        foreach ($this->getClusterMembers() as $member)
        {
            if ($member->getClusterMemberFuture())
            {
                $future[] = $member;
            }
        }

        return $future;
    }

    /**
     * @return array
     */
    public function getClusterActiveMembers(): array
    {
        $active = array();

        foreach ($this->getClusterMembers() as $member)
        {
            if ($member->getClusterMemberActive())
            {
                $active[] = $member;
            }
        }

        return $active;
    }

    /**
     * @return array
     */
    public function getClusterOldMembers(): array
    {
        $old = array();

        foreach ($this->getClusterMembers() as $member)
        {
            if ($member->getClusterMemberOld())
            {
                $old[] = $member;
            }
        }

        return $old;
    }
}
