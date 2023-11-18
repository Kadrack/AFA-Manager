<?php
// src/Entity/AddressBook.php
namespace App\Entity;

use App\Repository\AddressBookRepository;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Intl\Countries;

use Symfony\Component\Mime\Address;

/**
 * Class AddressBook
 */
#[ORM\Table(name: 'addressBook')]
#[ORM\Entity(repositoryClass: AddressBookRepository::class)]
class AddressBook
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $addressBookId;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $addressBookFirstname;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $addressBookName;

    /**
     * @var int|null
     */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $addressBookSex;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $addressBookAssociationName;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $addressBookAddress;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $addressBookZip;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $addressBookCity;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $addressBookCountry;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $addressBookEmail;

    /**
     * @var string|null
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $addressBookPhone;

    /**
     * @return int
     */
    public function getAddressBookId(): int
    {
        return $this->addressBookId;
    }

    /**
     * @param int $set
     *
     * @return $this
     */
    public function setAddressBookId(int $set): self
    {
        $this->addressBookId = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressBookFirstname(): ?string
    {
        return is_null($this->addressBookFirstname) ? 'N/A' : $this->addressBookFirstname;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setAddressBookFirstname(?string $set = null): self
    {
        $this->addressBookFirstname = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressBookName(): ?string
    {
        return is_null($this->addressBookName) ? 'N/A' : $this->addressBookName;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setAddressBookName(?string $set = null): self
    {
        $this->addressBookName = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return int|string|null
     */
    public function getAddressBookSex(bool $format = false): int|string|null
    {
        if (is_null($this->addressBookSex))
        {
            return $format ? 'N/A' : null;
        }

        return $format ? $this->getAddressBookSexText($this->addressBookSex) : $this->addressBookSex;
    }

    /**
     * @param int $id
     *
     * @return array|string
     */
    public function getAddressBookSexText(int $id = 0): array|string
    {
        $keys = array('Masculin' => 1, 'Féminin' => 2, 'Non défini' => 3);

        if ($id == 0)
        {
            return $keys;
        }
        elseif ($id > sizeof($keys))
        {
            return "Autre";
        }

        return array_search($id, $keys);
    }

    /**
     * @param int|null $set
     *
     * @return $this
     */
    public function setAddressBookSex(?int $set = null): self
    {
        $this->addressBookSex = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressBookAssociationName(): ?string
    {
        return $this->addressBookAssociationName;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setAddressBookAssociationName(?string $set = null): self
    {
        $this->addressBookAssociationName = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressBookFullName(): ?string
    {
        return is_null($this->addressBookAssociationName) ? $this->addressBookFirstname . ' ' . $this->addressBookName : $this->addressBookAssociationName;
    }

    /**
     * @return string|null
     */
    public function getAddressBookAddress(): ?string
    {
        return is_null($this->addressBookAddress) ? 'Non disponible' : $this->addressBookAddress;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setAddressBookAddress(?string $set = null): self
    {
        $this->addressBookAddress = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressBookZip(): ?string
    {
        return is_null($this->addressBookZip) ? 'Non disponible' : $this->addressBookZip;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setAddressBookZip(?string $set = null): self
    {
        $this->addressBookZip = $set;

        return $this;
    }

    /**
     * @return string
     */
    public function getAddressBookCity(): string
    {
        return is_null($this->addressBookCity) ? 'Non disponible' : $this->addressBookCity;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setAddressBookCity(?string $set = null): self
    {
        $this->addressBookCity = $set;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressBookCountry(): ?string
    {
        return is_null($this->addressBookCountry) ? 'Non disponible' : Countries::getName($this->addressBookCountry);
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setAddressBookCountry(?string $set = null): self
    {
        $this->addressBookCountry = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return Address|string|null
     */
    public function getAddressBookEmail(bool $format = false): Address|string|null
    {
        if (is_null($this->addressBookEmail))
        {
            return $format ? 'Aucune' : null;
        }

        return $format ? new Address($this->addressBookEmail, $this->getAddressBookFullName()) : $this->addressBookEmail;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setAddressBookEmail(?string $set): self
    {
        $this->addressBookEmail = $set;

        return $this;
    }

    /**
     * @param bool $format
     *
     * @return string|null
     */
    public function getAddressBookPhone(bool $format = false): string|null
    {
        if (is_null($this->addressBookPhone))
        {
            return $format ? 'Aucun' : null;
        }

        return $this->addressBookPhone;
    }

    /**
     * @param string|null $set
     *
     * @return $this
     */
    public function setAddressBookPhone(?string $set): self
    {
        $this->addressBookPhone = $set;

        return $this;
    }
}
