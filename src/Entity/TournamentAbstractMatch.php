<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TournamentMatchRepository")
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="kind", type="string")
 * @ORM\DiscriminatorMap({"simple" = "TournamentMatch", "royal" = "TournamentRoyalMatch"})
 * @ORM\Table(name="`TournamentMatch`")
 */
abstract class TournamentAbstractMatch
{
    const TYPE = 'abstractMatch';

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
     * @ORM\OneToMany(targetEntity="TournamentRound", mappedBy="match")
     */
    protected $rounds;

    /**
     * @ORM\ManyToOne(targetEntity="TournamentGroup", inversedBy="matches")
     */
    protected $group;

    /**
     * @ORM\OneToOne(targetEntity="TournamentKnockoutMatch", mappedBy="match")
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

    /**
     * Get winner
     *
     * @return \App\Entity\Participant $participant
     */
    abstract public function getWinner();

    /**
     * Get loser
     *
     * @return \App\Entity\Participant $participant
     */
    abstract public function getLoser();

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    abstract public function getParticipants();

    // End of Customs

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->state = \App\Entity\TournamentMatch::STATE_UPCOMING;
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
     * @param \App\Entity\TournamentRound $rounds
     * @return \App\Entity\TournamentMatch
     */
    public function addRound(\App\Entity\TournamentRound $rounds)
    {
        $this->rounds[] = $rounds;

        return $this;
    }

    /**
     * Remove rounds
     *
     * @param \App\Entity\TournamentRound $rounds
     */
    public function removeRound(\App\Entity\TournamentRound $rounds)
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
     * @return \App\Entity\TournamentMatch
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
     * @param \App\Entity\TournamentGroup $group
     * @return \App\Entity\TournamentMatch
     */
    public function setGroup(\App\Entity\TournamentGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \App\Entity\TournamentGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set koMatch
     *
     * @param \App\Entity\TournamentKnockoutMatch $koMatch
     * @return \App\Entity\TournamentKnockoutMatch
     */
    public function setKoMatch(\App\Entity\TournamentKnockoutMatch $koMatch = null)
    {
        $this->koMatch = $koMatch;

        return $this;
    }

    /**
     * Get koMatch
     *
     * @return \App\Entity\TournamentKnockoutMatch
     */
    public function getKoMatch()
    {
        return $this->koMatch;
    }

    /**
     * Get kind
     *
     * @return string
     */
    public function getKind()
    {
        $c = get_called_class();
        return $c::TYPE;
    }
}
