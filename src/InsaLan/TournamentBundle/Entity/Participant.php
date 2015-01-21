<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\ParticipantRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="kind", type="string")
 * @ORM\DiscriminatorMap({"team" = "Team", "player" = "Player"})
 */
abstract class Participant
{

    const STATUS_PENDING   = 0; // Not ready for validation
    const STATUS_WAITING   = 1; // Ready for validation, but no slot free
    const STATUS_VALIDATED = 2; // Validated

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="participants")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $tournament;

    /**
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="participants")
     */
    protected $groups;

    /**
     * @ORM\Column(type="integer")
     */
    protected $validated;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $placement;

    public function getParticipantType() {
        return "participant";
    }

    /**
     * Get id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->validated = false;
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public abstract function getName();

    /**
     * Set tournament
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournament
     * @return Participant
     */
    public function setTournament(\InsaLan\TournamentBundle\Entity\Tournament $tournament = null)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * Get tournament
     *
     * @return \InsaLan\TournamentBundle\Entity\Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * Add groups
     *
     * @param \InsaLan\TournamentBundle\Entity\Group $groups
     * @return Participant
     */
    public function addGroup(\InsaLan\TournamentBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \InsaLan\TournamentBundle\Entity\Group $groups
     */
    public function removeGroup(\InsaLan\TournamentBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }


    /**
     * Get validated
     *
     * @return boolean
     */
    public function getValidated()
    {
        return $this->validated;
    }

    public function setValidated($validated)
    {
        $this->validated = $validated;
        return $this;
    }


    /**
     * Set placement
     *
     * @param integer $placement
     * @return Participant
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;

        return $this;
    }

    /**
     * Get placement
     *
     * @return integer 
     */
    public function getPlacement()
    {
        return $this->placement;
    }
}
