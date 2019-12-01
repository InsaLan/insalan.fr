<?php

namespace InsaLan\ArchivesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stream
 *
 * @ORM\Table(name="`StreamArchives`")
 * @ORM\Entity(repositoryClass="InsaLan\ArchivesBundle\Entity\StreamRepository")
 */
class Stream
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="Edition")
     * @ORM\JoinColumn()
     */
    private $edition;

    /**
     * @var string
     *
     * @ORM\Column(name="album", type="string", length=255)
     */
    private $album;

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
     * @return Stream
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
     * Set url
     *
     * @param string $url
     * @return Stream
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }


    /**
     * Set edition
     *
     * @param \InsaLan\ArchivesBundle\Entity\Edition $edition
     * @return Stream
     */
    public function setEdition(\InsaLan\ArchivesBundle\Entity\Edition $edition)
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition
     *
     * @return \InsaLan\ArchivesBundle\Entity\Edition
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * Set album
     *
     * @param string $album
     * @return Stream
     */
    public function setAlbum($album)
    {
        $this->album = $album;

        return $this;
    }

    /**
     * Get album
     *
     * @return string
     */
    public function getAlbum()
    {
        return $this->album;
    }
}
