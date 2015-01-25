<?php
namespace InsaLan\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use InsaLan\TournamentBundle\Entity\Player;

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $credit = 0;

    /**
     * @ORM\Column(name="table_id", type="integer", nullable=true)
     */
    protected $table;

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstname;

     /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastname;

     /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phoneNumber;

    /**
     * @ORM\Column(type="date", nullable=true) 
     */
    private $birthdate;

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
     * Set credit
     *
     * @param integer $credit
     * @return User
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return integer
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set table
     *
     * @param integer $table
     * @return User
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get table
     *
     * @return integer
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Get Player
     *
     * @return Player
     */
    public function getPlayer() {
      return $this->player;
    }

    /**
     * Set Player
     *
     * @param InsaLan\TournamentBundle\Entity\Player
     * @return this
     */
    public function setPlayer(Player $p)
    {
        $this->player = $p;

        return $this;
    }

    public function removePlayer()
    {
        $this->player = null;

        return $this;
    }


    /**
     * Set firstname
     *
     * @param string $firstname
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return User
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     * @return User
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime 
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }
}
