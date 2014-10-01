<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
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
     * @Assert\NotBlank()
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Player", mappedBy="team")
     */
    protected $players;

    /**
     * @ORM\OneToOne(targetEntity="Player")
     */
    protected $captain;


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
        $this->players->add($players);
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
}