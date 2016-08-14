<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\TeamRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Team extends Participant
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=40)
     */
    protected $passwordSalt;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="Player", mappedBy="team")
     */
    protected $players;

    /**
     * @ORM\OneToOne(targetEntity="Player")
     */
    protected $captain;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $lastUpdated; // for callback lifecycle...

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

    protected $plainPassword;

    public function getParticipantType() {
        return "team";
    }

    public function __construct()
    {
        parent::__construct();
        $this->players = new ArrayCollection();
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
     * Set password
     *
     * @param string $password
     * @return Team
     */
    public function setPassword($password)
    {   

        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password salt
     *
     * @param string $passwordSalt
     * @return Team
     */
    public function setPasswordSalt($passwordSalt)
    {
        $this->passwordSalt = $passwordSalt;

        return $this;
    }

    /**
     * Get password salt
     *
     * @return string
     */
    public function getPasswordSalt()
    {
        return $this->passwordSalt;
    }

    /**
     * Generate password salt
     *
     * @return Team
     */
    public function generatePasswordSalt()
    {
        $this->setPasswordSalt(sha1(uniqid($this->getName(), true)));

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Team
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
     * Add players
     *
     * @param \InsaLan\TournamentBundle\Entity\Player $players
     * @return Team
     */
    public function addPlayer(\InsaLan\TournamentBundle\Entity\Player $players)
    {   

        if ($this->getPlayers()->count() >= $this->getTournament()->getTeamMaxPlayer()) {
            throw new \InsaLan\TournamentBundle\Exception\ControllerException("Cette équipe est pleine.");
        }
        $this->players->add($players);
        $this->setLastUpdated(time());
        return $this;
    }

    /**
     * Remove players
     *
     * @param \InsaLan\TournamentBundle\Entity\Player $players
     */
    public function removePlayer(\InsaLan\TournamentBundle\Entity\Player $players)
    {
        $this->players->removeElement($players);
        $this->setLastUpdated(time());
        return $this;
    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Check if a player is part of the team roster
     *
     * @return boolean
     */
    public function haveInPlayers(\InsaLan\TournamentBundle\Entity\Player $player)
    {
        foreach ($this->players as $teamPlayer) {
            if($teamPlayer === $player)
                return true;
        }
        return false;
    }

    /**
     * Set captain
     *
     * @param \InsaLan\TournamentBundle\Entity\Player $captain
     * @return Team
     */
    public function setCaptain(\InsaLan\TournamentBundle\Entity\Player $captain = null)
    {
        $this->captain = $captain;

        return $this;
    }

    /**
     * Get captain
     *
     * @return \InsaLan\TournamentBundle\Entity\Player 
     */
    public function getCaptain()
    {
        return $this->captain;
    }

    /**
     * Set validated
     *
     * @param integer $validated
     * @return Team
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
     * Set tournament
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournament
     * @return Team
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
     * @return Team
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
     * To String of Team
     *
     * @return Team
     */
    public function __toString() 
    {
        return $this->name;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }



    /**
     * Set lastUpdated
     *
     * @param integer $lastUpdated
     * @return Team
     */
    public function setLastUpdated($lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * Get lastUpdated
     *
     * @return integer 
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }
}
