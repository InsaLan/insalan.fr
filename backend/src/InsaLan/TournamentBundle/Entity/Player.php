<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use InsaLan\UserBundle\Entity\User;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\PlayerRepository")
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
     * @ORM\Column(name="game_name", type="string", length=50)
     * @Assert\NotBlank()
     */
    protected $gameName;

    /**
     * @ORM\ManyToOne(targetEntity="InsaLan\UserBundle\Entity\User")
     */
    protected $user;

    /**
     * @ORM\Column(name="game_id", type="integer", nullable=true, unique=false)
     */
    protected $gameId;

    /**
     * @ORM\Column(name="game_validated", type="boolean")
     */
    protected $gameValidated;

    /**
     * @ORM\Column(name="payment_done", type="boolean")
     */
    protected $paymentDone;

    /**
     * @ORM\Column(name="arrived", type="boolean")
     */
    protected $arrived;

    /**
     * @ORM\Column(name="game_avatar", type="integer", nullable=true)
     */
    protected $gameAvatar;

    /**
     * @ORM\ManyToMany(targetEntity="Team", inversedBy="players", cascade={"persist"})
     * @ORM\JoinColumn()
     */
    protected $team;

    /**
     * @ORM\ManyToOne(targetEntity="Registrable")
     */
    protected $pendingRegistrable;
    // this is a temporary variable when a player has not validated its account, and/or is waiting for a team.

    /**
     * @ORM\ManyToMany(targetEntity="InsaLan\UserBundle\Entity\MerchantOrder", mappedBy="players")
     */
    protected $merchantOrders;

    /**
     * @ORM\OneToOne(targetEntity="InsaLan\TicketingBundle\Entity\ETicket")
     */
    protected $eTicket;


    public function getParticipantType() {
        return "player";
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->team = new \Doctrine\Common\Collections\ArrayCollection();
        $this->gameValidated = false;
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
     * Get name
     *
     * @return string
     */
    public function getName() {
        if (isset($this->gameName)) {
            return $this->gameName;
        } else {
            return "Joueur sans nom";
        }
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
        $this->addTeam($team);
        return $this;
    }

    /**
     * Leave team
     *
     * @param \InsaLan\TournamentBundle\Entity\Team $team
     * @return Player
     */
    public function leaveTeam($team)
    {
        if ($team->getCaptain() !== null && $team->getCaptain()->getId() === $this->getId()) {
            $team->setCaptain(null);
        }
        $team->removePlayer($this);
        $this->removeTeam($team);
        return $this;
    }

    /**
     * @var \InsaLan\TournamentBundle\Entity\Tournament
     */
    protected $tournament;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $groups;

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
     * is set
     */
    public function isNamed($type) {
        return $this->gameName !== null;
    }

    /**
     * is validated
     */
    public function isValidated($type) {
        return $this->gameValidated;
    }

    /**
     * Add team
     *
     * @param \InsaLan\TournamentBundle\Entity\Team $team
     * @return Player
     */
    public function addTeam(\InsaLan\TournamentBundle\Entity\Team $team)
    {
        $this->team[] = $team;

        return $this;
    }

    /**
     * Remove team
     *
     * @param \InsaLan\TournamentBundle\Entity\Team $team
     */
    public function removeTeam(\InsaLan\TournamentBundle\Entity\Team $team)
    {
        $this->team->removeElement($team);
    }

    /**
     * Get team
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Is Registered For Tournament
     */
    public function isRegisteredForTournament($id) {
        return $this->getTeamByTournamentId($id) !== null;
    }

    /**
     * Get team by tournament id
     */
    public function getTeamByTournamentId($id) {
        foreach ($this->team as $team) {
            if ($team->getTournament()->getId() == $id) {
                return $team;
            }
        }
        return null;
    }

    /**
     * Set gameName
     *
     * @param string $gameName
     * @return Player
     */
    public function setGameName($gameName)
    {
        $this->gameName = $gameName;

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
     * Set gameId
     *
     * @param integer $gameId
     * @return Player
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * Get gameId
     *
     * @return integer
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * Set gameValidated
     *
     * @param boolean $gameValidated
     * @return Player
     */
    public function setGameValidated($gameValidated)
    {
        $this->gameValidated = $gameValidated;

        return $this;
    }

    /**
     * Get gameValidated
     *
     * @return boolean
     */
    public function getGameValidated()
    {
        return $this->gameValidated;
    }

    /**
     * Set gameAvatar
     *
     * @param integer $gameAvatar
     * @return Player
     */
    public function setGameAvatar($gameAvatar)
    {
        $this->gameAvatar = $gameAvatar;

        return $this;
    }

    /**
     * Get gameAvatar
     *
     * @return integer
     */
    public function getGameAvatar()
    {
        return $this->gameAvatar;
    }


    /**
     * Set pendingRegistrable
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $pendingRegistrable
     * @return Player
     */
    public function setPendingRegistrable(\InsaLan\TournamentBundle\Entity\Registrable $pendingRegistrable = null)
    {
        $this->pendingRegistrable = $pendingRegistrable;

        return $this;
    }

    /**
     * Get pendingRegistrable
     *
     * @return \InsaLan\TournamentBundle\Entity\Registrable
     */
    public function getPendingRegistrable()
    {
        return $this->pendingRegistrable;
    }

    /**
     * Set paymentDone
     *
     * @param boolean $paymentDone
     * @return Player
     */
    public function setPaymentDone($paymentDone)
    {
        $this->paymentDone = $paymentDone;

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

    public function getTeamForTournament(Tournament $tournament)
    {
        foreach ($this->team as $t) {
            if ($t->getTournament()->getId() == $tournament->getId())
                return $t;
        }
        return null;
    }

    public function isOk() {
        return $this->paymentDone && $this->gameValidated;
    }

    /**
     * Set arrived
     *
     * @param boolean $arrived
     * @return Player
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
     * Get Registrable
     *
     * @return Registrable
     */
    public function getRegistrable()
    {
        return $this->tournament !== null ? $this->tournament : $this->pendingRegistrable;
    }

    /**
     * Set eTicket
     *
     * @param integer eTicket
     * @return Player
     */
    public function setETicket($eTicket)
    {
        $this->eTicket = $eTicket;
        return $this;
    }

    /**
     * Get eTicket
     *
     * @return integer
     */
    public function getETicket()
    {
        return $this->eTicket;
    }

}
