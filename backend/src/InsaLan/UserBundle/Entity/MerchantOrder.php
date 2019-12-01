<?php

namespace InsaLan\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use InsaLan\TournamentBundle\Entity\Player;

/**
 * MerchantOrder
 *
 * @ORM\Entity(repositoryClass="InsaLan\UserBundle\Entity\MerchantOrderRepository")
 * @ORM\Table()
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
     * @ORM\ManyToOne(targetEntity="PaymentDetails",cascade={"persist"})
     */
    private $payment;

    /**
     * @ORM\ManyToMany(targetEntity="InsaLan\TournamentBundle\Entity\Player", inversedBy="merchantOrders")
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
     * Add players
     *
     * @param \InsaLan\TournamentBundle\Entity\Player $players
     * @return Group
     */
    public function addPlayer(\InsaLan\TournamentBundle\Entity\Player $players)
    {
        $this->players[] = $players;

        return $this;
    }

    /**
     * Remove players
     *
     * @param \InsaLan\TournamentBundle\Entity\Player $players
     */
    public function removePlayer(\InsaLan\TournamentBundle\Entity\Player $players)
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
     * @param \InsaLan\TournamentBundle\Entity\Player $players
     * @return bool
     */
    public function hasPlayer(\InsaLan\TournamentBundle\Entity\Player $player)
    {
        return $this->players->contains($player);
    }
}
