<?php

namespace InsaLan\ArchivesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Edition
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="InsaLan\ArchivesBundle\Entity\EditionRepository")
 */
class Edition
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="trailer", type="string", nullable=true, length=255)
     */
    private $trailerUrl;

    /**
     * @ORM\Column(type="boolean")
     */
    private $trailerAvailable;

    /**
     * @var string
     *
     * @ORM\Column(name="aftermovie", type="string", nullable=true, length=255)
     */
    private $aftermovieUrl;

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
     * @return Edition
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
     * Set year
     *
     * @param integer $year
     * @return Edition
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Edition
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set trailerUrl
     *
     * @param string $trailerUrl
     * @return Edition
     */
    public function setTrailerUrl($trailerUrl)
    {
        $this->trailerUrl = $trailerUrl;

        return $this;
    }

    /**
     * Get trailerUrl
     *
     * @return string
     */
    public function getTrailerUrl()
    {
        return $this->trailerUrl;
    }

    /**
     * Set trailerAvailable
     *
     * @param boolean $trailerAvailable
     * @return Edition
     */
    public function setTrailerAvailable($trailerAvailable)
    {
        $this->trailerAvailable = $trailerAvailable;

        return $this;
    }

    /**
     * Get trailerAvailable
     *
     * @return boolean
     */
    public function getTrailerAvailable()
    {
        return $this->trailerAvailable;
    }

    /**
     * Set aftermovieUrl
     *
     * @param string $aftermovieUrl
     * @return Edition
     */
    public function setAftermovieUrl($aftermovieUrl)
    {
        $this->aftermovieUrl = $aftermovieUrl;

        return $this;
    }

    /**
     * Get aftermovieUrl
     *
     * @return string
     */
    public function getAftermovieUrl()
    {
        return $this->aftermovieUrl;
    }

    public function __toString() {
        return $this->getName();
    }
}
