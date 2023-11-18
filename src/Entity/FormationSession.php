<?php
// src/Entity/FormationSession.php
namespace App\Entity;

use App\Repository\FormationSessionRepository;

use DateTime;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class FormationSession
 */
#[ORM\Table(name: 'formationSession')]
#[ORM\Entity(repositoryClass: FormationSessionRepository::class)]
class FormationSession
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $formationSessionId;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formationSessionDate = null;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $formationSessionType;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formationSessionOpen = null;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formationSessionClose = null;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'formationSessionCandidateSession', targetEntity: FormationSessionCandidate::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['formationSessionCandidateFirstname' => 'ASC', 'formationSessionCandidateName' => 'ASC'])]
    private ArrayCollection|Collection|null $formationSessionCandidates;

    /**
     * FormationSession constructor.
     */
    public function __construct()
    {
        $this->formationSessionCandidates = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getFormationSessionId(): int
    {
        return $this->formationSessionId;
    }

    /**
     * @param int $formationSessionId
     *
     * @return $this
     */
    public function setFormationSessionId(int $formationSessionId): self
    {
        $this->formationSessionId = $formationSessionId;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionDate(): ?DateTime
    {
        return $this->formationSessionDate;
    }

    /**
     * @param DateTime|null $formationSessionDate
     *
     * @return $this
     */
    public function setFormationSessionDate(?DateTime $formationSessionDate): self
    {
        $this->formationSessionDate = $formationSessionDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getFormationSessionType(): int
    {
        return $this->formationSessionType;
    }

    /**
     * @param int $formationSessionType
     *
     * @return $this
     */
    public function setFormationSessionType(int $formationSessionType): self
    {
        $this->formationSessionType = $formationSessionType;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionOpen(): ?DateTime
    {
        return $this->formationSessionOpen;
    }

    /**
     * @param DateTime|null $formationSessionOpen
     *
     * @return $this
     */
    public function setFormationSessionOpen(?DateTime $formationSessionOpen): self
    {
        $this->formationSessionOpen = $formationSessionOpen;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionClose(): ?DateTime
    {
        return $this->formationSessionClose;
    }

    /**
     * @param DateTime|null $formationSessionClose
     *
     * @return $this
     */
    public function setFormationSessionClose(?DateTime $formationSessionClose): self
    {
        $this->formationSessionClose = $formationSessionClose;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFormationSessionCandidates(): Collection
    {
        return $this->formationSessionCandidates;
    }

    /**
     * @param FormationSessionCandidate $formationSessionCandidate
     * @return $this
     */
    public function addFormationSessionCandidates(FormationSessionCandidate $formationSessionCandidate): self
    {
        if (!$this->formationSessionCandidates->contains($formationSessionCandidate)) {
            $this->formationSessionCandidates[] = $formationSessionCandidate;
            $formationSessionCandidate->setFormationSessionCandidateSession($this);
        }

        return $this;
    }

    /**
     * @param FormationSessionCandidate $formationSessionCandidate
     * @return $this
     */
    public function removeFormationSessionCandidates(FormationSessionCandidate $formationSessionCandidate): self
    {
        if ($this->formationSessionCandidates->contains($formationSessionCandidate)) {
            $this->formationSessionCandidates->removeElement($formationSessionCandidate);
            // set the owning side to null (unless already changed)
            if ($formationSessionCandidate->getFormationSessionCandidateSession() === $this) {
                $formationSessionCandidate->setFormationSessionCandidateSession(null);
            }
        }

        return $this;
    }

    /**
     * Custom function
     */

    /**
     * @return bool
     */
    public function getFormationSessionIsOpen(): bool
    {
        $today = new DateTime();

        if ($today >= $this->getFormationSessionOpen() && $today <= $this->getFormationSessionClose())
        {
            return true;
        }

        return false;
    }
}
