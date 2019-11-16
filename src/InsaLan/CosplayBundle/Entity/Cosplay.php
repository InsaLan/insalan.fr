<?php

namespace InsaLan\CosplayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cosplay
 * @ORM\Entity()
 * @ORM\Table(name="cosplay")
 */
class Cosplay
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="InsaLan\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="team", type="boolean")
     */
    private $team;

    /**
     * @ORM\OneToMany(targetEntity="Cosplayer", mappedBy="team")
     */
    private $members;

    /**
     * @ORM\Column(name="launch", type="enum", type="string", nullable=false, columnDefinition="enum('before', 'after')")
     */
    private $launch;

    /**
     * @var string
     *
     * @ORM\Column(name="setup", type="string", length=255)
     */
    private $setup;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="string", length=255)
     */
    private $details;

    /**
     * @var string
     *
     * @ORM\Column(name="soundtrack", type="string", length=255)
     */
    private $soundtrack;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \InsaLan\UserBundle\Entity\User $user
     *
     * @return Cosplay
     */
     public function setUser(\InsaLan\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \InsaLan\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Cosplay
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
     * Set team
     *
     * @param boolean $team
     *
     * @return Cosplay
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return bool
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set launch
     *
     * @param string $launch
     *
     * @return Cosplay
     */
    public function setLaunch($launch)
    {
        $this->launch = $launch;

        return $this;
    }

    /**
     * Get launch
     *
     * @return string
     */
    public function getLaunch()
    {
        return $this->launch;
    }

    /**
     * Set setup
     *
     * @param string $setup
     *
     * @return Cosplay
     */
    public function setSetup($setup)
    {
        $this->setup = $setup;

        return $this;
    }

    /**
     * Get setup
     *
     * @return string
     */
    public function getSetup()
    {
        return $this->setup;
    }

    /**
     * Set details
     *
     * @param string $details
     *
     * @return Cosplay
     */
    public function setDetails($details)
    {
        $this->details = $details;

        return $this;
    }

    /**
     * Get details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->details;
    }

    /**
     * Set soundtrack
     *
     * @param string $soundtrack
     *
     * @return Cosplay
     */
    public function setSoundtrack($soundtrack)
    {
        $this->soundtrack = $soundtrack;

        return $this;
    }

    /**
     * Get soundtrack
     *
     * @return string
     */
    public function getSoundtrack()
    {
        return $this->soundtrack;
    }
}
