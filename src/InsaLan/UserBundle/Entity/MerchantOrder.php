<?php

namespace InsaLan\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use InsaLan\TournamentBundle\Entity\Player;

/**
 * MerchantOrder
 *
 * @ORM\Entity(repositoryClass="InsaLan\UserBundle\Entity\MerchantOrderRepository")
 * @ORM\Table()
 * @ORM\Entity
 */
class MerchantOrder
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $merchant;

    /**
     * @ORM\ManyToOne(targetEntity="PaymentDetails")
     */
    private $payment;

    /**
     * @ORM\ManyToOne(targetEntity="InsaLan\TournamentBundle\Entity\Player")
     */
    private $player;


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
     * Set user
     *
     * @param \InsaLan\UserBundle\Entity\User $user
     * @return MerchantOrder
     */
    public function setMerchant(\InsaLan\UserBundle\Entity\User $user = null)
    {
        $this->merchant = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \InsaLan\UserBundle\Entity\User
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Set payment
     *
     * @param \InsaLan\UserBundle\Entity\PaymentDetails $payment
     * @return MerchantOrder
     */
    public function setPayment(\InsaLan\UserBundle\Entity\PaymentDetails $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \InsaLan\UserBundle\Entity\PaymentDetails
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set player
     *
     * @param \InsaLan\TournamentBundle\Entity\Player $player
     * @return MerchantOrder
     */
    public function setPlayer(\InsaLan\TournamentBundle\Entity\Player $player = null)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return \InsaLan\TournamentBundle\Entity\Player
     */
    public function getPlayer()
    {
        return $this->player;
    }
}
