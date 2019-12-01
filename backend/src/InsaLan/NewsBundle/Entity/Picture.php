<?php

namespace InsaLan\NewsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Picture
 *
 * @ORM\Table(name="news_picture")
 * @ORM\Entity()
 */
class Picture
{
  const UPLOAD_PATH = 'uploads/news/';

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
     * @ORM\Column(name="fileName", type="string", length=255, unique=true)
     */
    private $fileName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * Constructor
     */
    public function __construct()
    {
        // initialize upatedAt to current server time
        $this->updatedAt = new \DateTime("now");
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
     * Set fileName
     *
     * @param string $fileName
     *
     * @return Picture
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Picture
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * ORM\PostRemove
     */
    public function onPreRemove()
    {
        $name = $this->getUploadRootDir().$this->getFileName();
        if (file_exists($name))
        {
            unlink($name);
        }
    }

    public function upload() {
        if (null === $this->file) {
            return;
        }
        //$this->setFileName();
        $this->file->move($this->getUploadRootDir(), $this->fileName);
        $this->file = null;
    }

    public function getUploadDir()
    {
        return self::UPLOAD_PATH;
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

   public function refreshUpdated()
   {
      $this->setUpdatedAt(new \DateTime());
   }
}
