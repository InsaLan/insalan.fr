<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TournamentScore
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="TournamentRound", inversedBy="scores")
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
     * @return \App\Entity\TournamentRound
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * Set round
     *
     * @param \App\Entity\TournamentRound $round
     * @return \App\Entity\TournamentRound
     */
    public function setRound(\App\Entity\TournamentRound $round = null)
    {
        $this->round = $round;

        return $this;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return \App\Entity\TournamentRound
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
     * @param \App\Entity\Participant $participant
     * @return \App\Entity\TournamentRound
     */
    public function setParticipant(\App\Entity\Participant $participant = null)
    {
        $this->participant = $participant;

        return $this;
    }

    /**
     * Get participant
     *
     * @return \App\Entity\Participant
     */
    public function getParticipant()
    {
        return $this->participant;
    }

}
