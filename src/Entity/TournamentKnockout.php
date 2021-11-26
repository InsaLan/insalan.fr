<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Entity\TournamentKnockoutRepository")
 */
class TournamentKnockout
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
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $doubleElimination;

    /**
     * @ORM\OneToMany(targetEntity="TournamentKnockoutMatch", mappedBy="knockout")
     */
    protected $matches;

    /** For Bracket Generation **/

    protected $size;
    public function getSize() {
        return $this->size;
    }
    public function setSize($s) {
        $this->size = intval($s);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->doubleElimination = false;
    }

    public function __toString()
    {
        if($this->tournament)
            return $this->name . " " . $this->tournament->getName();

        else if ($this->name) {
            return $this->name;
          }
          // We have to return a string value
          return '';
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
     * @return Knockout
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
     * @param \App\Entity\Tournament $tournament
     * @return Knockout
     */
    public function setTournament(\App\Entity\Tournament $tournament = null)
    {
        $this->tournament = $tournament;

        return $this;
    }

    /**
     * Get tournament
     *
     * @return \App\Entity\Tournament
     */
    public function getTournament()
    {
        return $this->tournament;
    }

    /**
     * Set doubleElimination
     *
     * @param boolean $doubleElimination
     * @return Knockout
     */
    public function setDoubleElimination($doubleElimination)
    {
        $this->doubleElimination = $doubleElimination;

        return $this;
    }

    /**
     * Get doubleElimination
     *
     * @return boolean
     */
    public function getDoubleElimination()
    {
        return $this->doubleElimination;
    }
}
