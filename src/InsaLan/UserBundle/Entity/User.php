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

    public function __construct()
    {
        parent::__construct();
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $steamId;
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $battleTag;

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
     * Set steam id
     *
     * @param string $id
     * @return User
     */
    public function setSteamId($id)
    {
        $this->steamId = $id;

        return $this->steamId;
    }

    /**
     * Get steam id
     *
     * @return string 
     */
    public function getSteamId()
    {
        return $this->steamId;
    }
    /**
     * Set Battle Tag
     *
     * @param string $tag
     * @return User
     */
    public function setBattleTag($tag)
    {
        $this->battleTag = $tag;

        return $this->battleTag;
    }

    /**
     * Get battle tag (battle.net)
     *
     * @return string 
     */
    public function getBattleTag()
    {
        return $this->battleTag;
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
    public function getSteamDetails($steamKey) {
        if($this->steamId == null)
            return null;

        $json = file_get_contents('http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key='.$steamKey.'&steamids='.$this->steamId);
        if($json != null) {
            $obj = json_decode($json);
            return $obj->response->players[0];
            
        }
        return null;
    }
}
