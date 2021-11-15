<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Entity\SliderRepository")
 * @ORM\HasLifecycleCallbacks
 */
class NewsSlider
{
    const UPLOAD_PATH = 'uploads/slider/';

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
    protected $title;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $subtitle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $link;

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
     * Set title
     *
     * @param string $title
     * @return Slider
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set subtitle
     *
     * @param string $subtitle
     * @return Slider
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Slider
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
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
        $name = Slider::UPLOAD_PATH.DIRECTORY_SEPARATOR.$this->getId().'.png';
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
            Slider::UPLOAD_PATH,
            //$this->getFile()->getClientOriginalName()
            $this->getId().'.png'
        );

        $this->setFile(null);
    }
}
