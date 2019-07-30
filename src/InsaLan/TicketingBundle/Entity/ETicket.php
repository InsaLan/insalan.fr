<?php

namespace InsaLan\TicketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ETicket
 *
 * @ORM\Table(name="e_ticket")
 * @ORM\Entity()
 */
class ETicket
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
     * The associated user
     * @ORM\ManyToOne(targetEntity="InsaLan\UserBundle\Entity\User")
     */
    protected $user;

    /**
     * The related tournament
     * @ORM\ManyToOne(targetEntity="InsaLan\TournamentBundle\Entity\Tournament")
     */
    protected $tournament;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, unique=true)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sentAt", type="datetime", nullable=true)
     */
    private $sentAt;

    /**
     * @var bool
     *
     * @ORM\Column(name="isScanned", type="boolean")
     */
    private $isScanned = false;


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
     * Get user
     *
     * @return \InsaLan\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param \InsaLan\UserBundle\Entity\User $user
     * @return ETicket
     */
    public function setUser(\InsaLan\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
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
     * Set tournament
     *
     * @param \InsaLan\TournamentBundle\Entity\Tournament $tournament
     * @return ETicket
     */
    public function setTournament(\InsaLan\TournamentBundle\Entity\Tournament $tournament = null)
    {
        $this->tournament = $tournament;
        return $this;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return ETicket
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set sentAt
     *
     * @param \DateTime $sentAt
     *
     * @return ETicket
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * Get sentAt
     *
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Set isScanned
     *
     * @param boolean $isScanned
     *
     * @return ETicket
     */
    public function setIsScanned($isScanned)
    {
        $this->isScanned = $isScanned;

        return $this;
    }

    /**
     * Get isScanned
     *
     * @return bool
     */
    public function getIsScanned()
    {
        return $this->isScanned;
    }
}
