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
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private DateTime $formation_session_date;

    /**
     * @var int
     */
    #[ORM\Column(type: 'integer')]
    private int $formation_session_type;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $formation_session_candidate_open;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'date')]
    private DateTime $formation_session_candidate_close;

    /**
     * @var ArrayCollection|Collection|null
     */
    #[ORM\OneToMany(mappedBy: 'formation_session', targetEntity: Formation::class, cascade: ['persist'], orphanRemoval: true)]
    private ArrayCollection|Collection|null $formation_session_formations;

    /**
     * FormationSession constructor.
     */
    public function __construct()
    {
        $this->formation_session_formations = new ArrayCollection();
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
     * @return DateTime
     */
    public function getFormationSessionDate(): DateTime
    {
        return $this->formation_session_date;
    }

    /**
     * @param DateTime $formation_session_date
     * @return $this
     */
    public function setFormationSessionDate(DateTime $formation_session_date): self
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
     * @return DateTime
     */
    public function getFormationSessionCandidateOpen(): DateTime
    {
        return $this->formation_session_candidate_open;
    }

    /**
     * @param DateTime $formation_session_candidate_open
     * @return $this
     */
    public function setFormationSessionCandidateOpen(DateTime $formation_session_candidate_open): self
    {
        $this->formation_session_candidate_open = $formation_session_candidate_open;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFormationSessionCandidateClose(): DateTime
    {
        return $this->formation_session_candidate_close;
    }

    /**
     * @param DateTime $formation_session_candidate_close
     * @return $this
     */
    public function setFormationSessionCandidateClose(DateTime $formation_session_candidate_close): self
    {
        $this->formation_session_candidate_close = $formation_session_candidate_close;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getFormationSessionFormations(): Collection
    {
        return $this->formation_session_formations;
    }

    /**
     * @param Formation $formation
     * @return $this
     */
    public function addFormationSessionFormations(Formation $formation): self
    {
        if (!$this->formation_session_formations->contains($formation)) {
            $this->formation_session_formations[] = $formation;
            $formation->setFormationSession($this);
        }

        return $this;
    }

    /**
     * @param Formation $formation
     * @return $this
     */
    public function removeFormationSessionFormations(Formation $formation): self
    {
        if ($this->formation_session_formations->contains($formation)) {
            $this->formation_session_formations->removeElement($formation);
            // set the owning side to null (unless already changed)
            if ($formation->getFormationSession() === $this) {
                $formation->setFormationSession(null);
            }
        }

        return $this;
    }
}
