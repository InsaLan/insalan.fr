<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\GroupStageRepository")
 */
class GroupStage
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Tournament", fetch="EAGER")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $tournament;

    /**
     * @ORM\OneToMany(targetEntity="Group", mappedBy="stage")
     */
    protected $groups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return GroupStage
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set tournament
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournament
     * @return GroupStage
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
     * @return GroupStage
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

    public function __toString()
    {   
        if(!$this->getTournament()) return '';
        else return $this->getName() . ' ' . $this->getTournament()->__toString() ;
    }
}
