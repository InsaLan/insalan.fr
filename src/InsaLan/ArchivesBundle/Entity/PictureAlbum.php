<?php

namespace InsaLan\ArchivesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PictureAlbum
 *
 * @ORM\Table(name="`PictureAlbumArchives`")
 * @ORM\Entity()
 */
class PictureAlbum
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
     * @ORM\ManyToOne(targetEntity="Edition")
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
     * @return PictureAlbum
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
     * @return PictureAlbum
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
     * @param \InsaLan\ArchivesBundle\Entity\Edition $edition
     * @return PictureAlbum
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
}
