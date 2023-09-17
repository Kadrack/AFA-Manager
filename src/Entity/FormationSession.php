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
#[ORM\Table(name: 'formation_session')]
#[ORM\Entity(repositoryClass: FormationSessionRepository::class)]
class FormationSession
{
    /**
     * @var int
     */
    #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    private int $formation_session_id;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formation_session_date = null;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $formation_session_type;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formation_session_open = null;

    /**
     * @var DateTime|null
     */
    #[ORM\Column(type: 'date', nullable: true)]
    private ?DateTime $formation_session_close = null;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'formation_session_candidate_session', targetEntity: FormationSessionCandidate::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['formation_session_candidate_firstname' => 'ASC', 'formation_session_candidate_name' => 'ASC'])]
    private ArrayCollection|Collection|null $formation_session_candidates;

    /**
     * FormationSession constructor.
     */
    public function __construct()
    {
        $this->formation_session_candidates = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getFormationSessionId(): int
    {
        return $this->formation_session_id;
    }

    /**
     * @param int $formation_session_id
     * @return $this
     */
    public function setFormationSessionId(int $formation_session_id): self
    {
        $this->formation_session_id = $formation_session_id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionDate(): ?DateTime
    {
        return $this->formation_session_date;
    }

    /**
     * @param DateTime|null $formation_session_date
     * @return $this
     */
    public function setFormationSessionDate(?DateTime $formation_session_date): self
    {
        $this->formation_session_date = $formation_session_date;

        return $this;
    }

    /**
     * @return int
     */
    public function getFormationSessionType(): int
    {
        return $this->formation_session_type;
    }

    /**
     * @param int $formation_session_type
     * @return $this
     */
    public function setFormationSessionType(int $formation_session_type): self
    {
        $this->formation_session_type = $formation_session_type;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionOpen(): ?DateTime
    {
        return $this->formation_session_open;
    }

    /**
     * @param DateTime|null $formation_session_open
     * @return $this
     */
    public function setFormationSessionOpen(?DateTime $formation_session_open): self
    {
        $this->formation_session_open = $formation_session_open;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getFormationSessionClose(): ?DateTime
    {
        return $this->formation_session_close;
    }

    /**
     * @param DateTime|null $formation_session_close
     * @return $this
     */
    public function setFormationSessionClose(?DateTime $formation_session_close): self
    {
        $this->formation_session_close = $formation_session_close;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFormationSessionCandidates(): Collection
    {
        return $this->formation_session_candidates;
    }

    /**
     * @param FormationSessionCandidate $formationSessionCandidate
     * @return $this
     */
    public function addFormationSessionCandidates(FormationSessionCandidate $formationSessionCandidate): self
    {
        if (!$this->formation_session_candidates->contains($formationSessionCandidate)) {
            $this->formation_session_candidates[] = $formationSessionCandidate;
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
        if ($this->formation_session_candidates->contains($formationSessionCandidate)) {
            $this->formation_session_candidates->removeElement($formationSessionCandidate);
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
