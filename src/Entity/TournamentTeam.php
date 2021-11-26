<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Entity\TournamentTeamRepository")
 * @ORM\HasLifecycleCallbacks
 */
class TournamentTeam extends Participant
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
     * @var \App\Entity\Tournament
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
     * @return TournamentTeam
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
     * @return TournamentTeam
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
     * @return TournamentTeam
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
     * @param \App\Entity\Player $players
     * @return TournamentTeam
     */
    public function addPlayer(\App\Entity\Player $players)
    {

        if ($this->getPlayers()->count() >= $this->getTournament()->getTeamMaxPlayer()) {
            throw new \App\Exception\ControllerException("Cette équipe est pleine.");
        }
        $this->players->add($players);
        $this->setLastUpdated(time());
        return $this;
    }

    /**
     * Remove players
     *
     * @param \App\Entity\Player $players
     */
    public function removePlayer(\App\Entity\Player $players)
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
    public function haveInPlayers(\App\Entity\Player $player)
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
     * @param \App\Entity\Player $captain
     * @return TournamentTeam
     */
    public function setCaptain(\App\Entity\Player $captain = null)
    {
        $this->captain = $captain;

        return $this;
    }

    /**
     * Get captain
     *
     * @return \App\Entity\Player
     */
    public function getCaptain()
    {
        return $this->captain;
    }

    /**
     * Set tournament
     *
     * @param \App\Entity\Tournament $tournament
     * @return TournamentTeam
     */
    public function setTournament(\App\Entity\Tournament $tournament = null)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * Get tournament
     *
     * @return \App\Entity\Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * Add groups
     *
     * @param \App\Entity\TournamentGroup $groups
     * @return TournamentTeam
     */
    public function addGroup(\App\Entity\TournamentGroup $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \App\Entity\TournamentGroup $groups
     */
    public function removeGroup(\App\Entity\TournamentGroup $groups)
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
     * @return TournamentTeam
     */
    public function __toString()
    {
        if ($this->getName()) {
          return $this->getName();
        }
        // We have to return a string value
        return '';
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
     * @return TournamentTeam
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
