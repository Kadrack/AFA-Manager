<?php
// src/Entity/ClubHistory.php
namespace App\Entity;

use App\Repository\ClubHistoryRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ClubHistory
 */
#[ORM\Table(name: 'clubHistory')]
#[ORM\Entity(repositoryClass: ClubHistoryRepository::class)]
class ClubHistory
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $clubHistoryId;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $clubHistoryUpdate;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $clubHistoryStatus;

    /**
     * @var Club
     */
    #[ORM\ManyToOne(targetEntity: Club::class, cascade: ['persist'], inversedBy: 'clubHistories')]
    #[ORM\JoinColumn(name: 'clubHistory_join_club', referencedColumnName: 'clubId')]
    private Club $clubHistoryClub;

    /**
     * @return int
     */
    public function getClubHistoryId(): int
    {
        return $this->clubHistoryId;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubHistoryId(int $set): self
    {
        $this->clubHistoryId = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return DateTime|string
     */
    public function getClubHistoryUpdate(bool $format = false): DateTime|string
    {
        return $format ? $this->clubHistoryUpdate->format('d/m/Y') : $this->clubHistoryUpdate;
    }

    /**
     * @param DateTime $set
     *
     * @return $this
     */
    public function setClubHistoryUpdate(DateTime $set): self
    {
        $this->clubHistoryUpdate = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|null
     */
    public function getClubHistoryStatus(bool $format = false): ?int
    {
        $text = array('Ouvert', 'En attente', 'Fermé');

        return $format ? $text[$this->clubHistoryStatus] : $this->clubHistoryStatus;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setClubHistoryStatus(int $set): self
    {
        $this->clubHistoryStatus = $set;

        return $this;
    }

    /**
     * @return Club
     */
    public function getClubHistoryClub(): Club
    {
        return $this->clubHistoryClub;
    }

    /**
     * @param Club $set
     *
     * @return $this
     */
    public function setClubHistoryClub(Club $set): self
    {
        $this->clubHistoryClub = $set;

        return $this;
    }
}
