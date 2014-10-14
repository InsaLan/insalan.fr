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
    protected $name;

    /**
     * @ORM\OneToOne(targetEntity="InsaLan\UserBundle\Entity\User", mappedBy="player")
     */
    protected $user;

    /**
     * @ORM\Column(name="lol_id", type="integer", nullable=true, unique=true)
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
     * Set name
     *
     * @param string $name
     * @return Player
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
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
}
