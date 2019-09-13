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

    const STATUS_CANCELLED   = 0; // ETicket cancelled
    const STATUS_VALID   = 1; // Valid but not scanned
    const STATUS_SCANNED = 2; // Scanned

    public static function getStatuses()
    {
        return array (
            self::STATUS_CANCELLED => 'cancelled',
            self::STATUS_VALID => 'valid',
            self::STATUS_SCANNED => 'scanned',
        );
    }

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
     * @ORM\Column(type="integer")
     */
    protected $status;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->status = self::STATUS_VALID;
    }

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
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return ETicket
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Is the eticket scanned
     *
     * @return bool
     */
    public function isScanned()
    {
        return $this->status == self::STATUS_SCANNED;
    }

    /**
     * Is the eticket valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->status == self::STATUS_VALID;
    }

    /**
     * Is the eticket cancelled
     *
     * @return bool
     */
    public function isCancelled()
    {
        return $this->status == self::STATUS_CANCELLED;
    }
}
