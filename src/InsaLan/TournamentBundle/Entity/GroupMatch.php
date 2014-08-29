<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class GroupMatch
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="matches")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $group;

    /**
     * @ORM\ManyToOne(targetEntity="Match")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $match;

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
     * Set group
     *
     * @param \InsaLan\TournamentBundle\Entity\Group $group
     * @return GroupMatch
     */
    public function setGroup(\InsaLan\TournamentBundle\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \InsaLan\TournamentBundle\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set match
     *
     * @param \InsaLan\TournamentBundle\Entity\Match $match
     * @return GroupMatch
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
}
