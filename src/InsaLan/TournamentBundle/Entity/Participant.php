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
    const STATUS_PAYING_OFFLINE = 3; // Chose to pay offline, for single player tournaments

    public static function getStatuses()
    {
        return array (
            self::STATUS_PENDING => 'pending',
            self::STATUS_WAITING => 'waiting',
            self::STATUS_VALIDATED => 'validated',
            self::STATUS_PAYING_OFFLINE => 'paying_offline',
        );
    }

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
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $validationDate;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $placement;

    /**
     * @ORM\OneToOne(targetEntity="Manager", mappedBy="participant")
     */
    protected $manager;

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

        if($validated === $this::STATUS_VALIDATED || $validated === $this::STATUS_WAITING)
            $this->setValidationDate(new \DateTime("now"));

        return $this;
    }

    /**
     * Get validation date
     *
     * @return DateTime
     */
    public function getValidationDate()
    {
        return $this->validationDate;
    }

    public function setValidationDate($validationDate)
    {
        $this->validationDate = $validationDate;
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

    /**
     * Set manager
     *
     * @param integer manager
     * @return Participant
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     * Get Manager
     *
     * @return integer 
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Get Registrable
     *
     * @return Registrable
     */
    public function getRegistrable()
    {
        return $this->tournament;
    }
}
