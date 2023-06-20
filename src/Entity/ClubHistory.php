<?php
// src/Entity/ClubHistory.php
namespace App\Entity;

use App\Repository\ClubHistoryRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClubHistory
 */
#[ORM\Table(name: 'club_history')]
#[ORM\Entity(repositoryClass: ClubHistoryRepository::class)]
class ClubHistory
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $club_history_id;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $club_history_update;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $club_history_status;

    /**
     * @var Club|null
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'club_histories')]
    #[ORM\JoinColumn(name: 'club_history_join_club', referencedColumnName: 'club_id', nullable: false)]
    private ?Club $club_history;

    /**
     * @return int
     */
    public function getClubHistoryId(): int
    {
        return $this->club_history_id;
    }

    /**
     * @param int $club_history_id
     * @return $this
     */
    public function setClubHistoryId(int $club_history_id): self
    {
        $this->club_history_id = $club_history_id;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getClubHistoryUpdate(): DateTime
    {
        return $this->club_history_update;
    }

    /**
     * @param DateTime $club_history_update
     * @return $this
     */
    public function setClubHistoryUpdate(DateTime $club_history_update): self
    {
        $this->club_history_update = $club_history_update;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getClubHistoryStatus(): ?int
    {
        return $this->club_history_status;
    }

    /**
     * @param int $club_history_status
     * @return $this
     */
    public function setClubHistoryStatus(int $club_history_status): self
    {
        $this->club_history_status = $club_history_status;

        return $this;
    }

    /**
     * @return Club|null
     */
    public function getClubHistory(): ?Club
    {
        return $this->club_history;
    }

    public function setClubHistory(?Club $club_history): self
    {
        $this->club_history = $club_history;

        return $this;
    }
}
