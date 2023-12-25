<?php
// src/Entity/QrCodes.php
namespace App\Entity;

use App\Repository\QrCodesRepository;

use DateTime;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Bridge\Doctrine\Types\UuidType;

use Symfony\Component\Uid\Uuid;

/**
 * Class QrCodes
 */
#[ORM\Table(name: 'qrCodes')]
#[ORM\Entity(repositoryClass: QrCodesRepository::class)]
class QrCodes
{
    /**
     * @var Uuid
     */
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $qrCodesUuid;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date', options: ['default' => 'current_timestamp'])]
    private DateTime $qrCodesCreationDate;

    /**
     * @var bool
     */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $qrCodesIsValid = true;

    /**
     * @var Member|null
     */
    #[ORM\ManyToOne(targetEntity: Member::class, cascade: ['persist'], inversedBy: 'memberQrCodes')]
    #[ORM\JoinColumn(name: 'qrCodes_join_member', referencedColumnName: 'memberId', nullable: true, options: ['default' => null])]
    private Member|null $qrCodesMember = null;

    /**
     * QrCodes constructor.
     */
    public function __construct()
    {
        $this->setQrCodesCreationDate(new DateTime());
    }

    /**
     * @return Uuid
     */
    public function getQrCodesUuid(): Uuid
    {
        return $this->qrCodesUuid;
    }

    /**
     * @param Uuid $set
     *
     * @return $this
     */
    public function setQrCodesUuid(Uuid $set): self
    {
        $this->qrCodesUuid = $set;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getQrCodesCreationDate(): DateTime
    {
        return $this->qrCodesCreationDate;
    }

    /**
     * @param DateTime $set
     *
     * @return $this
     */
    public function setQrCodesCreationDate(DateTime $set): self
    {
        $this->qrCodesCreationDate = $set;

        return $this;
    }

    /**
     * @return bool
     */
    public function getQrCodesIsValid(): bool
    {
        return $this->qrCodesIsValid;
    }

    /**
     * @param bool $set
     *
     * @return $this
     */
    public function setQrCodesIsValid(bool $set): self
    {
        $this->qrCodesIsValid = $set;

        return $this;
    }

    /**
     * @return Member
     */
    public function getQrCodesMember(): Member
    {
        return $this->qrCodesMember;
    }

    /**
     * @param Member $set
     *
     * @return $this
     */
    public function setQrCodesMember(Member $set): self
    {
        $this->qrCodesMember = $set;

        return $this;
    }
}
