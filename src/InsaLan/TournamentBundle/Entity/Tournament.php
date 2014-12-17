<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="InsaLan\TournamentBundle\Entity\TournamentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Tournament
{
    const UPLOAD_PATH = 'uploads/tournament/';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="Participant", mappedBy="tournament")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    protected $participants;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $registrationOpen;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $registrationClose;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $registrationLimit;

    /**
     * @ORM\Column(type="enum", type="string", nullable=false)
     * ORM\Column(type="enum", type="string", nullable=false, columnDefinition="enum('lol', 'dota2', 'sc2', 'hs', 'csgo', 'manual')")
     */
    protected $type;

    /**
     * @ORM\Column(type="enum", type="string", nullable=false)
     * ORM\Column(type="enum", type="string", nullable=false, columnDefinition="enum('team', 'player')")
     */
    protected $participantType;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $teamMinPlayer;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $teamMaxPlayer;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $logoPath;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $webPrice;

    public function isOpenedInFuture() {
        $now = new \DateTime();
        return $this->registrationOpen > $now;
    }

    public function isOpenedNow() {
        $now = new \DateTime();
        return $this->registrationOpen <= $now && $this->registrationClose >= $now;
    }

    public function isOpenedInPast() {
        $now = new \DateTime();
        return $this->registrationClose < $now;
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
     * @return Tournament
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
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * ORM\PostPersist
     */
    public function onPostPersist()
    {
        $this->uploadFile();
    }

    /**
     * ORM\PostUpdate
     */
    public function onPostUpdate()
    {
        $this->uploadFile();
    }

    /**
     * ORM\PostRemove
     */
    public function onPreRemove()
    {
        $name = self::UPLOAD_PATH.DIRECTORY_SEPARATOR.$this->getId().'.png';
        if (file_exists($name))
        {
            unlink($name);
        }
    }

    private function uploadFile()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        $this->getFile()->move(
            self::UPLOAD_PATH,
            //$this->getFile()->getClientOriginalName()
            $this->getId().'.png'
        );

        $this->setFile(null);
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Tournament
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set registration_open
     *
     * @param \DateTime $registrationOpen
     * @return Tournament
     */
    public function setRegistrationOpen($registrationOpen)
    {
        $this->registrationOpen = $registrationOpen;

        return $this;
    }

    /**
     * Get registration_open
     *
     * @return \DateTime
     */
    public function getRegistrationOpen()
    {
        return $this->registrationOpen;
    }

    /**
     * Set registration_close
     *
     * @param \DateTime $registrationClose
     * @return Tournament
     */
    public function setRegistrationClose($registrationClose)
    {
        $this->registrationClose = $registrationClose;

        return $this;
    }

    /**
     * Get registration_close
     *
     * @return \DateTime
     */
    public function getRegistrationClose()
    {
        return $this->registrationClose;
    }

    /**
     * Set registration_limit
     *
     * @param \integer $registrationLimit
     * @return Tournament
     */
    public function setRegistrationLimit($registrationLimit)
    {
        $this->registrationLimit = $registrationLimit;

        return $this;
    }

    /**
     * Get registration_limit
     *
     * @return \integer
     */
    public function getRegistrationLimit()
    {
        return $this->registrationLimit;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Tournament
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set teamMinPlayer
     *
     * @param integer $teamMinPlayer
     * @return Tournament
     */
    public function setTeamMinPlayer($teamMinPlayer)
    {
        $this->teamMinPlayer = $teamMinPlayer;

        return $this;
    }

    /**
     * Get teamMinPlayer
     *
     * @return integer
     */
    public function getTeamMinPlayer()
    {
        return $this->teamMinPlayer;
    }

    /**
     * Set teamMaxPlayer
     *
     * @param integer $teamMaxPlayer
     * @return Tournament
     */
    public function setTeamMaxPlayer($teamMaxPlayer)
    {
        $this->teamMaxPlayer = $teamMaxPlayer;

        return $this;
    }

    /**
     * Get teamMaxPlayer
     *
     * @return integer
     */
    public function getTeamMaxPlayer()
    {
        return $this->teamMaxPlayer;
    }

    /**
     * Set participantType
     *
     * @param string $participantType
     * @return Tournament
     */
    public function setParticipantType($participantType)
    {
        $this->participantType = $participantType;

        return $this;
    }

    /**
     * Get participantType
     *
     * @return string
     */
    public function getParticipantType()
    {
        return $this->participantType;
    }
    /**
     * Constructor
     */
    public function __construct()
    {   
        $this->webPrice = 0;
        $this->participants = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add participants
     *
     * @param \InsaLan\TournamentBundle\Entity\Participant $participants
     * @return Tournament
     */
    public function addParticipant(\InsaLan\TournamentBundle\Entity\Participant $participants)
    {
        $this->participants[] = $participants;

        return $this;
    }

    /**
     * Remove participants
     *
     * @param \InsaLan\TournamentBundle\Entity\Participant $participants
     */
    public function removeParticipant(\InsaLan\TournamentBundle\Entity\Participant $participants)
    {
        $this->participants->removeElement($participants);
    }

    /**
     * Get participants
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    public function getUploadDir()
    {
        return 'uploads/tournament/logo/';
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    public function setFileName() {
        $this->logoPath = sha1(uniqid(mt_rand(), true)).'.'.$this->file->guessExtension();
    }

    public function upload() {
        if (null === $this->file) {
            return;
        }
        $this->setFileName();
        $this->file->move($this->getUploadRootDir(), $this->logoPath);
        $this->file = null;
    }

    /**
     * Set logoPath
     *
     * @param string $logoPath
     * @return Tournament
     */
    public function setLogoPath($logoPath)
    {
        $this->logoPath = $logoPath;

        return $this;
    }

    /**
     * Get logoPath
     *
     * @return string
     */
    public function getLogoPath()
    {
        return $this->logoPath;
    }

    /**
     * Set webPrice
     *
     * @param integer $webPrice
     * @return Tournament
     */
    public function setWebPrice($webPrice)
    {
        $this->webPrice = $webPrice;

        return $this;
    }

    /**
     * Get webPrice
     *
     * @return integer 
     */
    public function getWebPrice()
    {
        return $this->webPrice;
    }
}
