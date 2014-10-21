<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use InsaLan\UserBundle\Entity\User;

/**
 * @ORM\Entity
 */
class Player extends Participant
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    protected $lolName;

    /**
     * @ORM\OneToOne(targetEntity="InsaLan\UserBundle\Entity\User")
     */
    protected $user;

    /**
     * @ORM\Column(name="lol_id", type="integer", nullable=true, unique=false)
     */
    protected $lolId;

    /**
     * @ORM\Column(name="lol_id_validated", type="boolean")
     */
    protected $lolIdValidated;

    /**
     * @ORM\Column(name="lol_picture", type="integer", nullable=true)
     */
    protected $lolPicture;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="players", cascade={"persist"})
     * @ORM\JoinColumn()
     */
    protected $team;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lolIdValidated = false;
    }
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set lolName
     *
     * @param string $name
     * @return Player
     */
    public function setLolName($name)
    {
        $this->lolName = $name;

        return $this;
    }

    /**
     * Get lolName
     *
     * @return string 
     */
    public function getLolName()
    {
        return $this->lolName;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        if (isset($this->lolName)) {
            return $this->lolName;
        } else {
            return "Joueur sans nom";
        }
    }

    /**
     * Set lolId
     *
     * @param integer $lolId
     * @return User
     */
    public function setLolId($lolId)
    {
        $this->lolId = $lolId;
        return $this;
    }

    /**
     * Get lolId
     *
     * @return string
     */
    public function getLolId()
    {
        return $this->lolId;
    }

    /**
     * Set lolId_validated
     *
     * @param boolean $lolIdValidated
     * @return User
     */
    public function setLolIdValidated($lolIdValidated)
    {
        $this->lolIdValidated = $lolIdValidated;
        return $this;
    }

    /**
     * Get lolId_validated
     *
     * @return boolean
     */
    public function getLolIdValidated()
    {
        return $this->lolIdValidated;
    }

    /**
     * Set lolPicture
     *
     * @param integer $lolPicture
     * @return User
     */
    public function setLolPicture($lolPicture) {
        $this->lolPicture = $lolPicture;
        return $this;
    }

    /**
     * Get lolPicture
     *
     * @return integer
     */
    public function getLolPicture()
    {
        return $this->lolPicture;
    }

    /**
     * Set user
     *
     * @param \InsaLan\UserBundle\Entity\User $user
     * @return Player
     */
    public function setUser(\InsaLan\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \InsaLan\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Join team
     *
     * @param \InsaLan\TournamentBundle\Entity\Team $team
     * @return Player
     */
    public function joinTeam(\InsaLan\TournamentBundle\Entity\Team $team)
    {
        $this->team = $team;
        $team->addPlayer($this);
        return $this;
    }

    /**
     * Leave team
     *
     * @param \InsaLan\TournamentBundle\Entity\Team $team
     * @return Player
     */
    public function leaveTeam()
    {
        $this->team->removePlayer($this);
        $this->team = null;
        return $this;
    }

    /**
     * Get team
     *
     * @return \InsaLan\TournamentBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }
    /**
     * @var integer
     */
    protected $validated;

    /**
     * @var \InsaLan\TournamentBundle\Entity\Tournament
     */
    protected $tournament;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $groups;


    /**
     * Set validated
     *
     * @param integer $validated
     * @return Player
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Get validated
     *
     * @return integer 
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set team
     *
     * @param \InsaLan\TournamentBundle\Entity\Team $team
     * @return Player
     */
    public function setTeam(\InsaLan\TournamentBundle\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Set tournament
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournament
     * @return Player
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
     * @return Player
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
     * To string
     *
     * @return String
     */
    public function __toString() {
        return $this->getName();
    }

    /**
     * is validated
     */
    public function isValidated($type) {
        if ($type === 'lol') {
            return $this->lolIdValidated;
        }
    }
}
