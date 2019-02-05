<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\MatchRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="kind", type="string")
 * @ORM\DiscriminatorMap({"simple" = "Match"})
 * @ORM\Table(name="`Match`")
 */
abstract class AbstractMatch
{
    const STATE_UPCOMING = 0;
    const STATE_ONGOING  = 1;
    const STATE_FINISHED = 2;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $state;

    /**
     * @ORM\OneToMany(targetEntity="Round", mappedBy="match")
     */
    protected $rounds;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="matches")
     */
    protected $group;

    /**
     * @ORM\OneToOne(targetEntity="KnockoutMatch", mappedBy="match")
     */
    protected $koMatch;

    public function getTournament()
    {   
        if($this->getGroup())
            return $this->getGroup()->getTournament();
        elseif($this->getKoMatch())
            return $this->getKoMatch()->getKnockout()->getTournament();
        else
            return null;
    }

    public function getGroupStage()
    {   
        if($this->getGroup())
            return $this->getGroup()->getStage();
        else
            return null;
    }

    public function getExtraInfos()
    {   

        if($this->getGroup())
        {
            return $this->getGroup()->__toString();
        }

        elseif($this->getKoMatch())
        {
            return $this->getKoMatch()->__toString();
        }

        else return "?";
    }   

    abstract public function getWinner();

    abstract public function getLoser();

    // End of Customs

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->state = Match::STATE_UPCOMING;
        $this->rounds = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add rounds
     *
     * @param \InsaLan\TournamentBundle\Entity\Round $rounds
     * @return Match
     */
    public function addRound(\InsaLan\TournamentBundle\Entity\Round $rounds)
    {
        $this->rounds[] = $rounds;

        return $this;
    }

    /**
     * Remove rounds
     *
     * @param \InsaLan\TournamentBundle\Entity\Round $rounds
     */
    public function removeRound(\InsaLan\TournamentBundle\Entity\Round $rounds)
    {
        $this->rounds->removeElement($rounds);
    }

    /**
     * Get rounds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * Set state
     *
     * @param integer $state
     * @return Match
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set group
     *
     * @param \InsaLan\TournamentBundle\Entity\Group $group
     * @return Match
     */
    public function setGroup(\InsaLan\TournamentBundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \InsaLan\TournamentBundle\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set koMatch
     *
     * @param \InsaLan\TournamentBundle\Entity\KnockoutMatch $koMatch
     * @return KnockoutMatch
     */
    public function setKoMatch(\InsaLan\TournamentBundle\Entity\KnockoutMatch $koMatch = null)
    {
        $this->koMatch = $koMatch;

        return $this;
    }

    /**
     * Get koMatch
     *
     * @return \InsaLan\TournamentBundle\Entity\KnockoutMatch
     */
    public function getKoMatch()
    {
        return $this->koMatch;
    }
}
