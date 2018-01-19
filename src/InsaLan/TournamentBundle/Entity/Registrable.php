<?php
namespace InsaLan\TournamentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="kind", type="string")
 * @ORM\DiscriminatorMap({"tournament" = "Tournament", "bundle" = "Bundle"})
 */
abstract class Registrable
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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $locked;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $logoPath;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=false)
     */
    protected $webPrice;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=false)
     */
    protected $onlineIncreaseInPrice;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=false)
     */
    protected $onSitePrice;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $currency;

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

    public function isFull() {
        return $this->getFreeSlots() === 0;
    }

    public function isLocked() {
        return $this->locked != null;
    }

    public function checkLocked($authToken) {
        return $this->locked === $authToken;
    }

    public function getFreeSlots() {
        return $this->registrationLimit - $this->getValidatedSlots();
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
     * Constructor
     */
    public function __construct()
    {
        $this->webPrice = 0;

        // default values for Symphony
        $this->registrationOpen = new \DateTime("now");
        $this->registrationClose = (new \DateTime("now"))->modify("+1 week");
        $this->currency = "EUR";
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

    /**
     * Set onlineIncreaseInPrice
     *
     * @param integer $onlineIncreaseInPrice
     * @return Tournament
     */
    public function setOnlineIncreaseInPrice($onlineIncreaseInPrice)
    {
        $this->onlineIncreaseInPrice = $onlineIncreaseInPrice;

        return $this;
    }

    /**
     * Get onlineIncreaseInPrice
     *
     * @return integer
     */
    public function getOnlineIncreaseInPrice()
    {
        return $this->onlineIncreaseInPrice;
    }

    /**
     * Set currency
     *
     * @param string $currency
     * @return Tournament
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set onSitePrice
     *
     * @param integer $onSitePrice
     * @return Tournament
     */
    public function setOnSitePrice($onSitePrice)
    {
        $this->onSitePrice = $onSitePrice;

        return $this;
    }

    /**
     * Get onSitePrice
     *
     * @return integer
     */
    public function getOnSitePrice()
    {
        return $this->onSitePrice;
    }

    /**
     * Set locked
     *
     * @param string $locked
     * @return Tournament
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return string
     */
    public function getLocked()
    {
        return $this->locked;
    }

    public function isFree()
    {
        return $this->webPrice == 0 && $this->onlineIncreaseInPrice == 0;
    }

}
