<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\RoyalMatchRepository")
 */
class RoyalMatch extends AbstractMatch
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
     * @param \InsaLan\TournamentBundle\Entity\Participant $participants
     * @return Tournament
     */
    public function addParticipant(\InsaLan\TournamentBundle\Entity\Participant $participants)
    {
        $this->participants[] = $participants;

        return $this;
    }

    /**
     * Remove participants
     *
     * @param \InsaLan\TournamentBundle\Entity\Participant $participants
     */
    public function removeParticipant(\InsaLan\TournamentBundle\Entity\Participant $participants)
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

}
