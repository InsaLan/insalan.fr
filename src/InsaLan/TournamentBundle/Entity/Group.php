<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\GroupRepository")
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
     * @ORM\ManyToOne(targetEntity="Tournament")
     * @ORM\JoinColumn(name="tournament_id", referencedColumnName="id")
     */
    protected $tournament;

    /**
     * @ORM\OneToMany(targetEntity="GroupMatch", mappedBy="group")
     */
    protected $matches;

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
     * Set tournament
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournament
     * @return Group
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
     * Constructor
     */
    public function __construct()
    {
        $this->matches = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add matches
     *
     * @param \InsaLan\TournamentBundle\Entity\GroupMatch $matches
     * @return Group
     */
    public function addMatch(\InsaLan\TournamentBundle\Entity\GroupMatch $matches)
    {
        $this->matches[] = $matches;

        return $this;
    }

    /**
     * Remove matches
     *
     * @param \InsaLan\TournamentBundle\Entity\GroupMatch $matches
     */
    public function removeMatch(\InsaLan\TournamentBundle\Entity\GroupMatch $matches)
    {
        $this->matches->removeElement($matches);
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
        $participants = array();
        foreach ($this->getMatches() as $gm) {
            $m = $gm->getMatch();
            if (!isset($participants[$m->getPart1()->getId()])) {
                $participants[$m->getPart1()->getId()] = array(
                    'participant' => $m->getPart1(),
                    'won' => 0,
                    'lost' => 0,
                    'draw' => 0
                );
            }

            if (!isset($participants[$m->getPart2()->getId()])) {
                $participants[$m->getPart2()->getId()] = array(
                    'participant' => $m->getPart2(),
                    'won' => 0,
                    'lost' => 0,
                    'draw' => 0
                );
            }

            $score1 = $score2 = 0;

            foreach ($m->getRounds() as $r) {
                $score1 += $r->getScore1();
                $score2 += $r->getScore2();
            }

            if ($score1 < $score2) {
                $participants[$m->getPart1()->getId()]['lost'] += 1;
                $participants[$m->getPart2()->getId()]['won'] += 1;
            }
            else if ($score1 > $score2) {
                $participants[$m->getPart1()->getId()]['won'] += 1;
                $participants[$m->getPart2()->getId()]['lost'] += 1;
            }
            else {
                $participants[$m->getPart1()->getId()]['draw'] += 1;
                $participants[$m->getPart2()->getId()]['draw'] += 1;
            }
        }

        $this->participants = $participants;
        return $this;
    }
}
