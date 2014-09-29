<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"team" = "Team", "player" = "Player"})
 */
class Participant
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\ManyToOne(targetEntity="Tournament")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $tournament;

    /**
     * @ORM\ManyToMany(targetEntity="Group", mappedBy="participants")
     */
    protected $groups;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $validated = false;

    /**
     * Get id
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set tournament
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournament
     * @return Participant
     */
    public function setTournament(\InsaLan\TournamentBundle\Entity\Tournament $tournament = null)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * Get tournament
     *
     * @return \InsaLan\TournamentBundle\Entity\Tournament 
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * Add groups
     *
     * @param \InsaLan\TournamentBundle\Entity\Group $groups
     * @return Participant
     */
    public function addGroup(\InsaLan\TournamentBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \InsaLan\TournamentBundle\Entity\Group $groups
     */
    public function removeGroup(\InsaLan\TournamentBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }


    /**
     * Get validated
     *
     * @return boolean 
     */
    public function getValidated()
    {
        return $this->validated;
    }
}
