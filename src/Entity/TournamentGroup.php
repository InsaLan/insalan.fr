<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Entity\TournamentGroupRepository")
 * @ORM\Table(name="`Group`")
 */
class TournamentGroup
{
    const STATS_WINLOST = 0;
    const STATS_SCORE   = 1;

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
     * @ORM\OneToMany(targetEntity="TournamentAbstractMatch", cascade={"persist"}, mappedBy="group")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $matches;

    /**
     * @ORM\ManyToMany(targetEntity="Participant", inversedBy="groups")
     */
    protected $participants;

    /**
     * @ORM\ManyToOne(targetEntity="TournamentGroupStage", inversedBy="groups")
     */
    protected $stage;

    /**
     * @ORM\Column(type="integer")
     */
    protected $statsType = TournamentGroup::STATS_WINLOST;

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
     * @return TournamentGroup
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
     * @param \App\Entity\TournamentAbstractMatch $matches
     * @return TournamentGroup
     */
    public function addMatch(\App\Entity\TournamentAbstractMatch $matches)
    {
        $this->matches[] = $matches;

        return $this;
    }

    /**
     * Remove matches
     *
     * @param \App\Entity\TournamentAbstractMatch $matches
     */
    public function removeMatch(\App\Entity\TournamentAbstractMatch $matches)
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
            $stats = array('won' => 0, 'lost' => 0, 'draw' => 0, 'sum' => 0);
            $this->stats[$p->getId()] = $stats;
        }

        foreach ($this->getMatches() as $m) {
            if ($m->getKind() == 'simple') {
                $p1 = &$this->stats[$m->getPart1()->getId()];
                $p2 = &$this->stats[$m->getPart2()->getId()];

                $score1 = $score2 = 0;

                foreach ($m->getRounds() as $r) {
                    $score1 += $r->getScore($m->getPart1());
                    $score2 += $r->getScore($m->getPart2());
                    $p1['sum'] += $r->getScore($m->getPart1());
                    $p2['sum'] += $r->getScore($m->getPart2());
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
            } else {
                foreach ($m->getParticipants() as $pIt) {
                    $p = &$this->stats[$pIt->getId()];
                    $p['sum'] = 0;

                    foreach ($m->getRounds() as $r) {
                         $p['sum'] += $r->getScore($pIt);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Set stage
     *
     * @param \App\Entity\TournamentGroupStage $stage
     * @return TournamentGroup
     */
    public function setStage(\App\Entity\TournamentGroupStage $stage = null)
    {
        $this->stage = $stage;

        return $this;
    }

    /**
     * Get stage
     *
     * @return \App\Entity\TournamentGroupStage
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
     * @param \App\Entity\Participant $participants
     * @return TournamentGroup
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

    public function hasParticipant(\App\Entity\Participant $participant)
    {
        return $this->participants->contains($participant);
    }

    /**
     * Set statsType
     *
     * @param integer $statsType
     * @return TournamentMatch
     */
    public function setStatsType($statsType)
    {
        $this->statsType = $statsType;

        return $this;
    }

    /**
     * Get statsType
     *
     * @return integer
     */
    public function getStatsType()
    {
        return $this->statsType;
    }

    /**
     * Get the match between two participants in this group only
     *
     * @param  \App\Entity\Participant $A
     * @param  \App\Entity\Participant $B
     * @return \App\Entity\TournamentMatch       or null if not available
     */
    public function getMatchBetween(\App\Entity\Participant $A, \App\Entity\Participant $B)
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

    /**
     * Get participants sorted by scores (if statsType is STATS_SCORE), by default otherwise
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSortedParticipants()
    {
        $array = $this->participants->toArray();

        if ($this->getStatsType() == TournamentGroup::STATS_SCORE)
        {
            usort($array, function ($a, $b) {
                return $this->stats[$a->getId()]["sum"] < $this->stats[$b->getId()]["sum"] ? 1 : -1;
            });
        }
        
        return new \Doctrine\Common\Collections\ArrayCollection($array);
    }
}
