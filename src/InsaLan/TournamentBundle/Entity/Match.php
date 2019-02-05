<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\MatchRepository")
 */
class Match extends AbstractMatch
{
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

    // CUSTOM FUNCTIONS FOR ADMIN

    public function __toString()
    {
        return $this->part1 . " vs " . $this->part2;
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

    public function getLoser()
    {
        $winner = $this->getWinner();
        if(!$winner) return null;
        return ($this->getPart1() === $winner ? $this->getPart2() : $this->getPart1());

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

}
