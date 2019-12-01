<?php

namespace InsaLan\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use InsaLan\TournamentBundle\Entity\Tournament;

/**
 * Discount
 *
 * @ORM\Entity(repositoryClass="InsaLan\UserBundle\Entity\DiscountRepository")
 * @ORM\Table()
 * @ORM\Entity
 */
class Discount
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount = 0;

    /**
     * @ORM\ManyToOne(targetEntity="InsaLan\TournamentBundle\Entity\Registrable")
     */
    private $registrable;


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
     * @return Discount
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
     * Set amount
     *
     * @param integer $amount
     * @return Discount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set registrable
     *
     * @param \InsaLan\TournamentBundle\Entity\Registrable $registrable
     * @return Discount
     */
    public function setRegistrable(\InsaLan\TournamentBundle\Entity\Registrable $registrable = null)
    {
        $this->registrable = $registrable;

        return $this;
    }

    /**
     * Get registrable
     *
     * @return \InsaLan\TournamentBundle\Entity\Registrable
     */
    public function getRegistrable()
    {
        return $this->registrable;
    }
}
