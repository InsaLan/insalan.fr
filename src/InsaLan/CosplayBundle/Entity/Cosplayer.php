<?php

namespace InsaLan\CosplayBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cosplayer
 * @ORM\Entity()
 * @ORM\Table(name="Cosplayer")
 */
class Cosplayer
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
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="pseudo", type="string", length=255)
     */
    private $pseudo;

    /**
     * @var bool
     *
     * @ORM\Column(name="usePseudo", type="boolean")
     */
    private $usePseudo;

    /**
     * @var bool
     *
     * @ORM\Column(name="adult", type="boolean")
     */
    private $adult;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * @var int
     *
     * @ORM\Column(name="postalCode", type="integer")
     */
    private $postalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="characterCosplayed", type="string", length=255)
     */
    private $characterCosplayed;

    /**
     * @var string
     *
     * @ORM\Column(name="origin", type="string", length=255)
     */
    private $origin;

    /**
     * @var string
     *
     * @ORM\Column(name="picturePath", type="string", length=255, unique=true, nullable=true)
     */
    private $picturePath;

    /**
     * @var string
     *
     * @ORM\Column(name="pictureRightPath", type="string", length=255, unique=true, nullable=true)
     */
    private $pictureRightPath;

    /**
     * @var string
     *
     * @ORM\Column(name="parentalConsentPath", type="string", length=255, unique=true, nullable=true)
     */
    private $parentalConsentPath;

    /**
     * @ORM\ManyToOne(targetEntity="Cosplay", inversedBy="members")
     */
    private $group;

    public function __construct()
    {
        $this->usePseudo = false;
        $this->adult = false;
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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Participant
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
     *
     * @return Participant
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
     * Set pseudo
     *
     * @param string $pseudo
     *
     * @return Participant
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set usePseudo
     *
     * @param boolean $usePseudo
     *
     * @return Participant
     */
    public function setUsePseudo($usePseudo)
    {
        $this->usePseudo = $usePseudo;

        return $this;
    }

    /**
     * Get usePseudo
     *
     * @return bool
     */
    public function getUsePseudo()
    {
        return $this->usePseudo;
    }

    /**
     * Set adult
     *
     * @param boolean $adult
     *
     * @return Participant
     */
    public function setAdult($adult)
    {
        $this->adult = $adult;

        return $this;
    }

    /**
     * Get adult
     *
     * @return bool
     */
    public function getAdult()
    {
        return $this->adult;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Participant
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Participant
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set postalCode
     *
     * @param integer $postalCode
     *
     * @return Participant
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return int
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * Set characterCosplayed
     *
     * @param string $characterCosplayed
     *
     * @return Participant
     */
    public function setCharacterCosplayed($characterCosplayed)
    {
        $this->characterCosplayed = $characterCosplayed;

        return $this;
    }

    /**
     * Get characterCosplayed
     *
     * @return string
     */
    public function getCharacterCosplayed()
    {
        return $this->characterCosplayed;
    }

    /**
     * Set origin
     *
     * @param string $origin
     *
     * @return Participant
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set picturePath
     *
     * @param string $picturePath
     *
     * @return Cosplayer
     */
    public function setPicturePath($picturePath)
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    /**
     * Get picturePath
     *
     * @return string
     */
    public function getPicturePath()
    {
        return $this->picturePath;
    }

    /**
     * Set pictureRightPath
     *
     * @param string $pictureRightPath
     *
     * @return Cosplayer
     */
    public function setPictureRightPath($pictureRightPath)
    {
        $this->pictureRightPath = $pictureRightPath;

        return $this;
    }

    /**
     * Get pictureRightPath
     *
     * @return string
     */
    public function getPictureRightPath()
    {
        return $this->pictureRightPath;
    }

    /**
     * Set parentalConsentPath
     *
     * @param string $parentalConsentPath
     *
     * @return Cosplayer
     */
    public function setParentalConsentPath($parentalConsentPath)
    {
        $this->parentalConsentPath = $parentalConsentPath;

        return $this;
    }

    /**
     * Get parentalConsentPath
     *
     * @return string
     */
    public function getParentalConsentPath()
    {
        return $this->parentalConsentPath;
    }

    /**
     * Set group
     *
     * @param string $group
     *
     * @return Cosplayer
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }
}
