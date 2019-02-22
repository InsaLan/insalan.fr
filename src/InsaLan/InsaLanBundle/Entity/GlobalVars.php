<?php

namespace InsaLan\InsaLanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GlobalVars
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="InsaLan\InsaLanBundle\Entity\GlobalVarsRepository")
 */
class GlobalVars
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
     * @ORM\Column(name="globalKey", type="string", length=255)
     */
    private $globalKey;

    /**
     * @var string
     *
     * @ORM\Column(name="globalValue", type="string", length=255)
     */
    private $globalValue;


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
     * Set globalKey
     *
     * @param string $globalKey
     * @return GlobalVars
     */
    public function setGlobalKey($globalKey)
    {
        $this->globalKey = $globalKey;

        return $this;
    }

    /**
     * Get globalKey
     *
     * @return string
     */
    public function getGlobalKey()
    {
        return $this->globalKey;
    }

    /**
     * Set globalValue
     *
     * @param string $globalValue
     * @return GlobalVars
     */
    public function setGlobalValue($globalValue)
    {
        $this->globalValue = $globalValue;

        return $this;
    }

    /**
     * Get globalValue
     *
     * @return string
     */
    public function getGlobalValue()
    {
        return $this->globalValue;
    }
}
