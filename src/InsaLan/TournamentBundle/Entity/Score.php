<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Score
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Round", inversedBy="scores")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $round;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Participant")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $participant;

    /**
     * @ORM\Column(type="integer")
     */
    protected $score;

    // CUSTOM FUNCTIONS FOR ADMIN

    public function __toString()
    {
        return $this->getParticipant()->getName() . " : " . $this->getScore();
    }

    // End Of Customs

    /**
     * Get round
     *
     * @return Round
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set round
     *
     * @param \InsaLan\TournamentBundle\Entity\Round $round
     * @return Round
     */
    public function setRound(\InsaLan\TournamentBundle\Entity\Round $round = null)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return Round
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set participant
     *
     * @param \InsaLan\TournamentBundle\Entity\Participant $participant
     * @return Round
     */
    public function setParticipant(\InsaLan\TournamentBundle\Entity\Participant $participant = null)
    {
        $this->participant = $participant;

        return $this;
    }

    /**
     * Get participant
     *
     * @return \InsaLan\TournamentBundle\Entity\Participant
     */
    public function getParticipant()
    {
        return $this->participant;
    }

}
