<?php

/**
 * Uses Tree extension for Doctrine (Nested Set)
 * 
 * https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/tree.md
 */

namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\KnockoutMatchRepository")
 * @Gedmo\Tree(type="nested")
 */
class KnockoutMatch
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Knockout")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $knockout;

    /**
     * @ORM\OneToOne(targetEntity="Match")
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
     * @ORM\ManyToOne(targetEntity="KnockoutMatch", inversedBy="children")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="KnockoutMatch", mappedBy="parent")
     * @ORM\OrderBy({"left" = "ASC"})
     */
    private $children;

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
     * @param \InsaLan\TournamentBundle\Entity\Knockout $knockout
     * @return KnockoutMatch
     */
    public function setKnockout(\InsaLan\TournamentBundle\Entity\Knockout $knockout = null)
    {
        $this->knockout = $knockout;

        return $this;
    }

    /**
     * Get knockout
     *
     * @return \InsaLan\TournamentBundle\Entity\Knockout
     */
    public function getKnockout()
    {
        return $this->knockout;
    }

    /**
     * Set match
     *
     * @param \InsaLan\TournamentBundle\Entity\Match $match
     * @return KnockoutMatch
     */
    public function setMatch(\InsaLan\TournamentBundle\Entity\Match $match = null)
    {
        $this->match = $match;

        return $this;
    }

    /**
     * Get match
     *
     * @return \InsaLan\TournamentBundle\Entity\Match
     */
    public function getMatch()
    {
        return $this->match;
    }

    /**
     * Set parent
     *
     * @param \InsaLan\TournamentBundle\Entity\KnockoutMatch $parent
     * @return KnockoutMatch
     */
    public function setParent(\InsaLan\TournamentBundle\Entity\KnockoutMatch $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \InsaLan\TournamentBundle\Entity\KnockoutMatch
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \InsaLan\TournamentBundle\Entity\KnockoutMatch $children
     * @return KnockoutMatch
     */
    public function addChildren(\InsaLan\TournamentBundle\Entity\KnockoutMatch $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \InsaLan\TournamentBundle\Entity\KnockoutMatch $children
     */
    public function removeChildren(\InsaLan\TournamentBundle\Entity\KnockoutMatch $children)
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
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add children
     *
     * @param \InsaLan\TournamentBundle\Entity\KnockoutMatch $children
     * @return KnockoutMatch
     */
    public function addChild(\InsaLan\TournamentBundle\Entity\KnockoutMatch $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \InsaLan\TournamentBundle\Entity\KnockoutMatch $children
     */
    public function removeChild(\InsaLan\TournamentBundle\Entity\KnockoutMatch $children)
    {
        $this->children->removeElement($children);
    }
}
