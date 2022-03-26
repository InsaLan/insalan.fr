<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TournamentRoyalMatchRepository")
 */
class TournamentRoyalMatch extends TournamentAbstractMatch
{
    const TYPE = 'royal';

    /**
     * @ORM\ManyToMany(targetEntity="Participant")
     */
    protected $participants;

    // CUSTOM FUNCTIONS FOR ADMIN

    public function __toString()
    {
        return "Battle royale " . $this->getExtraInfos();
    }

    public function getWinner()
    {
        return null;
    }

    public function getLoser()
    {
        return null;
    }

    // End of Customs

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->participants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add participants
     *
     * @param \App\Entity\Participant $participants
     * @return \App\Entity\Tournament
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
     * Check if has participant
     *
     * @return bool
     */
    public function hasParticipant(\App\Entity\Participant $participant)
    {
        return $this->participants->contains($participant);
    }

}
