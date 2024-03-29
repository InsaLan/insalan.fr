<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use App\Entity\Player;
use App\Entity\User;
use App\Entity\UserPaymentDetails;

/**
 * MerchantOrder
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserMerchantOrderRepository")
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
     * @ORM\ManyToOne(targetEntity="UserPaymentDetails",cascade={"persist"})
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
     * @param User $user
     * @return UserMerchantOrder
     */
    public function setMerchant(User $user = null)
    {
        $this->merchant = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * Set payment
     *
     * @param UserPaymentDetails $payment
     * @return UserMerchantOrder
     */
    public function setPayment(UserPaymentDetails $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment
     *
     * @return UserPaymentDetails
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Add players
     *
     * @param \App\Entity\Player $players
     * @return \App\Entity\TournamentGroup
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
