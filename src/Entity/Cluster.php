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
    private int $clusterId;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $clusterName;

    /**
     * @var int
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private int $clusterFreeTraining = 0;

    /**
     * @var int
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private int $clusterUseTitle = 0;

    /**
     * @var int
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private int $clusterUseEmail = 0;

    /**
     * @var int
     */
    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private int $clusterGiveAccess = 0;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'clusterMemberCluster', targetEntity: ClusterMember::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['clusterMemberTitle' => 'ASC', 'clusterMemberDateIn' => 'ASC'])]
    private ArrayCollection|Collection|null $clusterMembers;

    /**
     * Cluster constructor.
     */
    public function __construct()
    {
        $this->clusterMembers = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getClusterId(): int
    {
        return $this->clusterId;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClusterId(int $set): self
    {
        $this->clusterId = $set;

        return $this;
    }

    /**
     * @return string
     */
    public function getClusterName(): string
    {
        return $this->clusterName;
    }

    /**
     * @param string $set
     *
     * @return $this
     */
    public function setClusterName(string $set): self
    {
        $this->clusterName = $set;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClusterFreeTraining(): bool
    {
        return $this->clusterFreeTraining;
    }

    /**
     * @param bool $set
     *
     * @return $this
     */
    public function setClusterFreeTraining(bool $set = false): self
    {
        $this->clusterFreeTraining = $set;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClusterUseTitle(): bool
    {
        return $this->clusterUseTitle;
    }

    /**
     * @param bool $set
     *
     * @return $this
     */
    public function setClusterUseTitle(bool $set = false): self
    {
        $this->clusterUseTitle = $set;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClusterUseEmail(): bool
    {
        return $this->clusterUseEmail;
    }

    /**
     * @param bool $set
     *
     * @return $this
     */
    public function setClusterUseEmail(bool $set = false): self
    {
        $this->clusterUseEmail = $set;

        return $this;
    }

    /**
     * @return bool
     */
    public function getClusterGiveAccess(): bool
    {
        return $this->clusterGiveAccess;
    }

    /**
     * @param bool $set
     *
     * @return $this
     */
    public function setClusterGiveAccess(bool $set = false): self
    {
        $this->clusterGiveAccess = $set;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getClusterMembers(): Collection
    {
        return $this->clusterMembers;
    }

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
