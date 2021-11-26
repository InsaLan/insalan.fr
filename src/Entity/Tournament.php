<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Entity\TournamentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Tournament extends Registrable
{
    const TYPE = 'tournament';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true, length=10)
     */
    protected $shortName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $rules;

    /**
     * @ORM\OneToMany(targetEntity="Participant", mappedBy="tournament")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $participants;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $tournamentOpen;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $tournamentClose;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $placement;

    /**
     * @ORM\Column(type="enum", type="string", nullable=false)
     * ORM\Column(type="enum", type="string", nullable=false, columnDefinition="enum('lol', 'dota2', 'sc2', 'hs', 'csgo', 'ow', 'sfv', 'dbfz', 'fbr', 'manual')")
     */
    protected $type;

    /**
     * @ORM\Column(type="enum", type="string", nullable=false)
     * ORM\Column(type="enum", type="string", nullable=false, columnDefinition="enum('team', 'player')")
     */
    protected $participantType;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $teamMinPlayer;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $teamMaxPlayer;

    /**
     * Maximum number of manager allowed on the tournament.
     * Only for solo tournaments at the moment.
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $maxManager;

	/**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $loginType;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $playerInfos;

    /**
     * @ORM\ManyToMany(targetEntity="TournamentBundle", mappedBy="tournaments")
     */
    protected $bundles;

    public function isPending() {
        $now = new \DateTime();
        return $this->tournamentOpen > $now;
    }

    public function isPlaying() {
        $now = new \DateTime();
        return $this->tournamentOpen <= $now && $this->tournamentClose >= $now;
    }

    public function isClosed() {
        $now = new \DateTime();
        return $this->tournamentClose < $now;
    }

    public function getValidatedSlots() {
        $nb = 0;
        foreach($this->getParticipants() as $p) {
            $nb += ($p->getValidated() === Participant::STATUS_VALIDATED ? 1 : 0);
        }
        return $nb;
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

    public function __toString()
    {      
        if ($this->getName()) {
          return $this->getName();
        }
        // We have to return a string value
        return '';
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Tournament
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set teamMinPlayer
     *
     * @param integer $teamMinPlayer
     * @return Tournament
     */
    public function setTeamMinPlayer($teamMinPlayer)
    {
        $this->teamMinPlayer = $teamMinPlayer;

        return $this;
    }

    /**
     * Get teamMinPlayer
     *
     * @return integer
     */
    public function getTeamMinPlayer()
    {
        return $this->teamMinPlayer;
    }

    /**
     * Set teamMaxPlayer
     *
     * @param integer $teamMaxPlayer
     * @return Tournament
     */
    public function setTeamMaxPlayer($teamMaxPlayer)
    {
        $this->teamMaxPlayer = $teamMaxPlayer;

        return $this;
    }

    /**
     * Get teamMaxPlayer
     *
     * @return integer
     */
    public function getTeamMaxPlayer()
    {
        return $this->teamMaxPlayer;
    }

    /**
     * Get the maximum allowed number of manager
     *
     * @return integer
     */
    public function getMaxManager()
    {
        return $this->maxManager;
    }

    /**
     * Set the maximum allowed number of manager
     */
    public function setMaxManager($maxManager)
    {
        $this->maxManager = $maxManager;

        return $this;
    }


    /**
     * Set participantType
     *
     * @param string $participantType
     * @return Tournament
     */
    public function setParticipantType($participantType)
    {
        $this->participantType = $participantType;

        return $this;
    }

    /**
     * Get participantType
     *
     * @return string
     */
    public function getParticipantType()
    {
        return $this->participantType;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->participants = new \Doctrine\Common\Collections\ArrayCollection();
        $this->placement = false;

        // default values for Symphony
        $this->tournamentOpen = (new \DateTime("now"))->modify("+1 week");
        $this->tournamentClose = (new \DateTime("now"))->modify("+1 week")->modify("+10 hour");
    }

    /**
     * Add participants
     *
     * @param \App\Entity\Participant $participants
     * @return Tournament
     */
    public function addParticipant(\App\Entity\Participant $participants)
    {
        $this->participants[] = $participants;

        return $this;
    }

    /**
     * Remove participants
     *
     * @param \App\Entity\Participant $participants
     */
    public function removeParticipant(\App\Entity\Participant $participants)
    {
        $this->participants->removeElement($participants);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Set tournamentOpen
     *
     * @param \DateTime $tournamentOpen
     * @return Tournament
     */
    public function setTournamentOpen($tournamentOpen)
    {
        $this->tournamentOpen = $tournamentOpen;

        return $this;
    }

    /**
     * Get tournamentOpen
     *
     * @return \DateTime
     */
    public function getTournamentOpen()
    {
        return $this->tournamentOpen;
    }

    /**
     * Set tournamentClose
     *
     * @param \DateTime $tournamentClose
     * @return Tournament
     */
    public function setTournamentClose($tournamentClose)
    {
        $this->tournamentClose = $tournamentClose;

        return $this;
    }

    /**
     * Get tournamentClose
     *
     * @return \DateTime
     */
    public function getTournamentClose()
    {
        return $this->tournamentClose;
    }

    /**
     * Set placement
     *
     * @param boolean $placement
     * @return Tournament
     */
    public function setPlacement($placement)
    {
        $this->placement = $placement;

        return $this;
    }

    /**
     * Get placement
     *
     * @return boolean
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     * @return Tournament
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set playerInfos
     *
     * @param string $playerInfos
     * @return Tournament
     */
    public function setPlayerInfos($playerInfos)
    {
        $this->playerInfos = $playerInfos;

        return $this;
    }

    /**
     * Get playerInfos
     *
     * @return string
     */
    public function getPlayerInfos()
    {
        return $this->playerInfos;
    }

    /**
     * Set rules
     *
     * @param string $rules
     * @return Tournament
     */
    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Get rules
     *
     * @return string
     */
    public function getRules()
    {
        return $this->rules;
    }

	/**
     * Set login type
     *
     * @param string $loginType
     * @return Tournament
     */
    public function setLoginType($loginType)
    {
        $this->loginType = $loginType;

        return $this;
    }

    /**
     * Get login type
     *
     * @return string
     */
    public function getLoginType()
    {
        return $this->loginType;
    }
}
