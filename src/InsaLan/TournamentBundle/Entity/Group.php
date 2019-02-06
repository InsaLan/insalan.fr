<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\GroupRepository")
 * @ORM\Table(name="`Group`")
 */
class Group
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
     * @ORM\OneToMany(targetEntity="Match", cascade={"persist"}, mappedBy="group")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $matches;

    /**
     * @ORM\ManyToMany(targetEntity="Participant", inversedBy="groups")
     */
    protected $participants;

    /**
     * @ORM\ManyToOne(targetEntity="GroupStage", inversedBy="groups")
     */
    protected $stage;

    // CUSTOM FUNCTIONS FOR ADMIN

    public function getTournament()
    {
        return $this->getStage()->getTournament();
    }

    public function getExtraInfos()
    {
        return count($this->getParticipants());
    }

    // End of custom functions

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
     * @return Group
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
     * Constructor
     */
    public function __construct()
    {
        $this->matches = new \Doctrine\Common\Collections\ArrayCollection();
        $this->participants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add matches
     *
     * @param \InsaLan\TournamentBundle\Entity\Match $matches
     * @return Group
     */
    public function addMatch(\InsaLan\TournamentBundle\Entity\Match $matches)
    {
        $this->matches[] = $matches;

        return $this;
    }

    /**
     * Remove matches
     *
     * @param \InsaLan\TournamentBundle\Entity\Match $matches
     */
    public function removeMatch(\InsaLan\TournamentBundle\Entity\Match $matches)
    {
        return $this->matches->removeElement($matches);
    }

    /**
     * Get matches
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMatches()
    {
        return $this->matches;
    }

    public function countWins()
    {
        // Initialize statistics
        $this->stats = array();
        foreach ($this->participants as $p) {
            $stats = array('won' => 0, 'lost' => 0, 'draw' => 0);
            $this->stats[$p->getId()] = $stats;
        }

        foreach ($this->getMatches() as $m) {
            $p1 = &$this->stats[$m->getPart1()->getId()];
            $p2 = &$this->stats[$m->getPart2()->getId()];

            $score1 = $score2 = 0;

            foreach ($m->getRounds() as $r) {
                $score1 += $r->getScore($m->getPart1());
                $score2 += $r->getScore($m->getPart2());
            }

            if ($score1 < $score2) {
                ++$p1['lost'];
                ++$p2['won'];
            }
            else if ($score1 > $score2) {
                ++$p1['won'];
                ++$p2['lost'];
            }
            else {
                ++$p1['draw'];
                ++$p2['draw'];
            }
        }

        return $this;
    }

    /**
     * Set stage
     *
     * @param \InsaLan\TournamentBundle\Entity\GroupStage $stage
     * @return Group
     */
    public function setStage(\InsaLan\TournamentBundle\Entity\GroupStage $stage = null)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return \InsaLan\TournamentBundle\Entity\GroupStage
     */
    public function getStage()
    {
        return $this->stage;
    }

    public function __toString()
    {   
        if($this->getStage() === null) return "";
        return $this->getName() . " " . $this->getStage()->__toString();
    }

    /**
     * Add participants
     *
     * @param \InsaLan\TournamentBundle\Entity\Participant $participants
     * @return Group
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
        return $this->participants->removeElement($participants);

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

    public function hasParticipant(\InsaLan\TournamentBundle\Entity\Participant $participant)
    {
        return $this->participants->contains($participant);
    }

    /**
     * Get the match between two participants in this group only
     *
     * @param  \InsaLan\TournamentBundle\Entity\Participant $A
     * @param  \InsaLan\TournamentBundle\Entity\Participant $B
     * @return \InsaLan\TournamentBundle\Entity\Match       or null if not available
     */
    public function getMatchBetween(\InsaLan\TournamentBundle\Entity\Participant $A, \InsaLan\TournamentBundle\Entity\Participant $B)
    {
        $matches = $this->getMatches()->toArray();

        foreach($matches as $match)
        {
            if(($match->getPart1() === $A && $match->getPart2() === $B) ||
               ($match->getPart1() === $B && $match->getPart2() === $A))
                return $match;
        }

        return null;
    }
}
