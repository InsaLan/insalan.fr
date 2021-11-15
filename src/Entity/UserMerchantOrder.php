<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Player;

/**
 * MerchantOrder
 *
 * @ORM\Entity(repositoryClass="App\Entity\MerchantOrderRepository")
 * @ORM\Table()
 */
class UserMerchantOrder
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
     * @ORM\ManyToOne(targetEntity="PaymentDetails",cascade={"persist"})
     */
    private $payment;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Player", inversedBy="merchantOrders")
     */
    private $players;


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
     * @param \App\Entity\User $user
     * @return MerchantOrder
     */
    public function setMerchant(\App\Entity\User $user = null)
    {
        $this->merchant = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \App\Entity\User
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Set payment
     *
     * @param \App\Entity\PaymentDetails $payment
     * @return MerchantOrder
     */
    public function setPayment(\App\Entity\PaymentDetails $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return \App\Entity\PaymentDetails
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Add players
     *
     * @param \App\Entity\Player $players
     * @return Group
     */
    public function addPlayer(\App\Entity\Player $players)
    {
        $this->players[] = $players;

        return $this;
    }

    /**
     * Remove players
     *
     * @param \App\Entity\Player $players
     */
    public function removePlayer(\App\Entity\Player $players)
    {
        return $this->players->removeElement($players);

    }

    /**
     * Get players
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Check if has player
     *
     * @param \App\Entity\Player $players
     * @return bool
     */
    public function hasPlayer(\App\Entity\Player $player)
    {
        return $this->players->contains($player);
    }
}
