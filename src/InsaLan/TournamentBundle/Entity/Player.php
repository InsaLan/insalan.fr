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
    protected $lol_id;

    /**
     * @ORM\Column(name="lol_id_validated", type="boolean")
     */
    protected $lol_id_validated = false;

    /**
     * @ORM\Column(name="lol_picture", type="integer", nullable=true)
     */
    protected $lol_picture;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="players", cascade={"persist"})
     * @ORM\JoinColumn()
     */
    protected $team;

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
     * Set lol_id
     *
     * @param integer $lol_id
     * @return User
     */
    public function setLolId($lol_id) {
      $this->lol_id = $lol_id;
      return $this;
    }

    /**
     * Get lol_id
     *
     * @return string
     */
    public function getLolId()
    {
        return $this->lol_id;
    }

    /**
     * Set lol_id_validated
     *
     * @param boolean $lol_id_validated
     * @return User
     */
    public function setLolIdValidated($lol_id_validated) {
      $this->lol_id_validated = $lol_id_validated;
      return $this;
    }

    /**
     * Get lol_id_validated
     *
     * @return boolean
     */
    public function getLolIdValidated()
    {
        return $this->lol_id_validated;
    }

    /**
     * Set lol_picture
     *
     * @param integer $lol_picture
     * @return User
     */
    public function setLolPicture($lol_picture) {
      $this->lol_picture = $lol_picture;
      return $this;
    }

    /**
     * Get lol_picture
     *
     * @return integer
     */
    public function getLolPicture()
    {
        return $this->lol_picture;
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
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
