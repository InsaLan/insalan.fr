<?php

/**
 * Uses Tree extension for Doctrine (Nested Set)
 * 
 * https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/tree.md
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\TournamentMatch;
use App\Entity\TournamentKnockout;
/**
 * @ORM\Entity(repositoryClass="App\Repository\TournamentKnockoutMatchRepository")
 * @Gedmo\Tree(type="nested")
 */
class TournamentKnockoutMatch
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="TournamentKnockout", inversedBy="matches")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $knockout;

    /**
     * @ORM\OneToOne(targetEntity="TournamentMatch", inversedBy="koMatch")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $match;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Gedmo\TreeRoot
     */
    private $root;

    /**
     * @ORM\Column(name="lvl", type="integer")
     * @Gedmo\TreeLevel
     */
    private $level;

    /**
     * @ORM\Column(name="lft", type="integer")
     * @Gedmo\TreeLeft
     */
    private $left;

    /**
     * @ORM\Column(name="rgt", type="integer")
     * @Gedmo\TreeRight
     */
    private $right;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="TournamentKnockoutMatch", inversedBy="children")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="TournamentKnockoutMatch", mappedBy="parent")
     * @ORM\OrderBy({"left" = "ASC"})
     */
    private $children;

    /**
     * @ORM\ManyToOne(targetEntity="TournamentKnockoutMatch")
     */
    private $loserDestination;


    /**
     * @ORM\Column(type="boolean") 
     */
    private $oddNode; // if true, this node is waiting for a player from winner bracket


    public $cancelWait;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->oddNode = false;
        $this->autoVictory = false;
        $this->cancelWait = false;
    }

    /**
     * @return String A french description of this match level in the tree
     */
    public function getFrenchLevel()
    {   
        if(!$this->getKnockout()->getDoubleElimination()) {
            switch ($this->getLevel()) {
                case 0: return "Finale";
                case 1: return "Demi-finale";
                case 2: return "1/4 de finale";
                case 3: return "1/8 de finale";
                case 4: return "1/16 de finale";
                case 5: return "1/32 de finale";
                case 6: return "1/64 de finale";
                default: return "?";
            }
        } else {
            if($this->getLevel() === 0) return "GRANDE FINALE";
            $p = "W.B.";
            if(!$this->getLoserDestination())
                $p = "L.B.";

            switch ($this->getLevel()) {
                case 1: return "Finale " . $p;
                case 2: return "Demi-finale " . $p;
                case 3: return "1/4 de finale " . $p;
                case 4: return "1/8 de finale " . $p;
                case 5: return "1/16 de finale " . $p;
                case 6: return "1/32 de finale " . $p;
                case 7: return "1/64 de finale " . $p;
                default: return "?";
            }
        }

    }

    public function __toString()
    {
        return $this->getFrenchLevel() . " " . $this->getKnockout()->__toString();
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
     * Set root
     *
     * @param integer $root
     * @return KnockoutMatch
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set level
     *
     * @param integer $level
     * @return KnockoutMatch
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set left
     *
     * @param integer $left
     * @return KnockoutMatch
     */
    public function setLeft($left)
    {
        $this->left = $left;

        return $this;
    }

    /**
     * Get left
     *
     * @return integer
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * Set right
     *
     * @param integer $right
     * @return KnockoutMatch
     */
    public function setRight($right)
    {
        $this->right = $right;

        return $this;
    }

    /**
     * Get right
     *
     * @return integer
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * Set knockout
     *
     * @param TournamentKnockout $knockout
     * @return KnockoutMatch
     */
    public function setKnockout(TournamentKnockout $knockout = null)
    {
        $this->knockout = $knockout;

        return $this;
    }

    /**
     * Get knockout
     *
     * @return TournamentKnockout
     */
    public function getKnockout()
    {
        return $this->knockout;
    }

    /**
     * Set match
     *
     * @param TournamentMatch $match
     * @return KnockoutMatch
     */
    public function setMatch(TournamentMatch $match = null)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match
     *
     * @return TournamentMatch
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set parent
     *
     * @param TournamentKnockoutMatch $parent
     * @return KnockoutMatch
     */
    public function setParent(TournamentKnockoutMatch $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return TournamentKnockoutMatch
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param TournamentKnockoutMatch $children
     * @return KnockoutMatch
     */
    public function addChildren(TournamentKnockoutMatch $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param TournamentKnockoutMatch $children
     */
    public function removeChildren(TournamentKnockoutMatch $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * Add children
     *
     * @param TournamentKnockoutMatch $children
     * @return KnockoutMatch
     */
    public function addChild(TournamentKnockoutMatch $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param TournamentKnockoutMatch $children
     */
    public function removeChild(TournamentKnockoutMatch $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Set loserDestination
     *
     * @param TournamentKnockoutMatch $loserDestination
     * @return KnockoutMatch
     */
    public function setLoserDestination(TournamentKnockoutMatch $loserDestination = null)
    {
        $this->loserDestination = $loserDestination;

        return $this;
    }

    /**
     * Get loserDestination
     *
     * @return TournamentKnockoutMatch 
     */
    public function getLoserDestination()
    {
        return $this->loserDestination;
    }

    /**
     * Set oddNode
     *
     * @param boolean $oddNode
     * @return KnockoutMatch
     */
    public function setOddNode($oddNode)
    {
        $this->oddNode = $oddNode;

        return $this;
    }

    /**
     * Get oddNode
     *
     * @return boolean 
     */
    public function getOddNode()
    {
        return $this->oddNode;
    }

}
