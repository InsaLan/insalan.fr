<?php

namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Manager : This class handle the teams associated managers
 * There can be only one manager per team !
 * Pretty similar to the Player entity
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\ManagerRepository")
 */
class Manager
{
    // Define the price payed by managers
    const ONLINE_PRICE = 5;  // price to be paid online in EUR
    const ONSITE_PRICE = 10; // price to be paid onsite in EUR

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * The associated user
     * @ORM\ManyToOne(targetEntity="InsaLan\UserBundle\Entity\User")
     */
    protected $user;

    /**
     * The related tournament, used for team filtering
     * @ORM\ManyToOne(targetEntity="Tournament")
     */
    protected $tournament;

    /**
     * The associated player or team
     * @ORM\OneToOne(targetEntity="Participant", inversedBy="manager")
     */
    protected $participant;

    /**
     * In-game name of the manager
     * @ORM\Column(name="game_name", type="string", length=50)
     * @Assert\NotBlank()
     */
    protected $gameName;

    /**
     * @ORM\Column(name="payment_done", type="boolean")
     */
    protected $paymentDone;

    /**
     * @ORM\Column(name="arrived", type="boolean")
     */
    protected $arrived;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->paymentDone = false;
        $this->arrived = false;
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
     * Get user
     *
     * @return \InsaLan\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param \InsaLan\UserBundle\Entity\User $user
     * @return Manager
     */
    public function setUser(\InsaLan\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
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
     * Set tournament
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournament
     * @return Manager
     */
    public function setTournament(\InsaLan\TournamentBundle\Entity\Tournament $tournament = null)
    {
        $this->tournament = $tournament;
        return $this;
    }

    /**
     * Get realted participant
     *
     * @return \InsaLan\UserBundle\Entity\Participant
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * Set related participant
     *
     * @param \InsaLan\UserBundle\Entity\Participant $participant
     * @return Manager
     */
    public function setParticipant(\InsaLan\TournamentBundle\Entity\Participant $participant = null)
    {
        $this->participant = $participant;
        return $this;
    }

    /**
     * Get gameName
     *
     * @return string
     */
    public function getGameName()
    {
        return $this->gameName;
    }

    /**
     * Set gameName
     *
     * @param string $gameName
     * @return Manager
     */
    public function setGameName($gameName)
    {
        $this->gameName = $gameName;

        return $this;
    }

    /**
     * Get paymentDone
     *
     * @return boolean
     */
    public function getPaymentDone()
    {
        return $this->paymentDone;
    }

    /**
     * Set paymentDone
     *
     * @param boolean $paymentDone
     * @return Manager
     */
    public function setPaymentDone($paymentDone)
    {
        $this->paymentDone = $paymentDone;

        return $this;
    }

    public function isOk() {
        return $this->paymentDone;
    }

    /**
     * Set arrived
     *
     * @param boolean $arrived
     * @return Manager
     */
    public function setArrived($arrived)
    {
        $this->arrived = $arrived;

        return $this;
    }

    /**
     * Get arrived
     *
     * @return boolean
     */
    public function getArrived()
    {
        return $this->arrived;
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
     * is set
     */
    public function isNamed($type) {
        return $this->gameName !== null;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        if (isset($this->gameName)) {
            return $this->gameName;
        } else {
            return "Manager sans nom";
        }
    }
}
