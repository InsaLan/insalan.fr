<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
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
}
