<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\MatchRepository")
 * @ORM\Table(name="`Match`")
 */
class Match
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
     * @ORM\ManyToOne(targetEntity="Participant")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $part1;

    /**
     * @ORM\ManyToOne(targetEntity="Participant")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $part2;

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

    // CUSTOM FUNCTIONS FOR ADMIN

    public function __toString()
    {
        return $this->part1 . " vs " . $this->part2;
    }

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

    public function getWinner()
    {   

        if($this->getPart1() === null && $this->getPart2() !== null)
            return $this->getPart2();

        if($this->getPart2() === null && $this->getPart1() !== null)
            return $this->getPart1();

        $won1 = $this->getScore1();
        $won2 = $this->getScore2();

        if($won1 === $won2) return null;
        return ($won1 > $won2 ? $this->getPart1() : $this->getPart2());
    }

    public function getScore1()
    {   
        $won = 0;
        foreach($this->getRounds() as $r)
        {
            if($r->getScore1() > $r->getScore2())
                $won++;
        }
        return $won;
    }

    public function getScore2()
    {
        $won = 0;
        foreach($this->getRounds() as $r)
        {
            if($r->getScore2() > $r->getScore1())
                $won++;
        }
        return $won;
    }

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
     * Set part1
     *
     * @param \InsaLan\TournamentBundle\Entity\Participant $part1
     * @return Match
     */
    public function setPart1(\InsaLan\TournamentBundle\Entity\Participant $part1 = null)
    {
        $this->part1 = $part1;

        return $this;
    }

    /**
     * Get part1
     *
     * @return \InsaLan\TournamentBundle\Entity\Participant
     */
    public function getPart1()
    {
        return $this->part1;
    }

    /**
     * Set part2
     *
     * @param \InsaLan\TournamentBundle\Entity\Participant $part2
     * @return Match
     */
    public function setPart2(\InsaLan\TournamentBundle\Entity\Participant $part2 = null)
    {
        $this->part2 = $part2;

        return $this;
    }

    /**
     * Get part2
     *
     * @return \InsaLan\TournamentBundle\Entity\Participant
     */
    public function getPart2()
    {
        return $this->part2;
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
