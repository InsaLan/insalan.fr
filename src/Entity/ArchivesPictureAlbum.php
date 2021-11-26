<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PictureAlbum
 *
 * @ORM\Table(name="`PictureAlbumArchives`")
 * @ORM\Entity()
 */
class ArchivesPictureAlbum
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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="ArchivesEdition")
     * @ORM\JoinColumn()
     */
    private $edition;

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
     * Set url
     *
     * @param string $url
     * @return ArchivesPictureAlbum
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
     * Set name
     *
     * @param string $name
     * @return ArchivesPictureAlbum
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
     * Set edition
     *
     * @param \App\Entity\ArchivesEdition $edition
     * @return ArchivesPictureAlbum
     */
    public function setEdition(\App\Entity\ArchivesEdition $edition)
    {
        $this->edition = $edition;

        return $this;
    }

    /**
     * Get edition
     *
     * @return \App\Entity\ArchivesEdition
     */
    public function getEdition()
    {
        return $this->edition;
    }
}
