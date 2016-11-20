<?php
namespace InsaLan\PizzaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="InsaLan\PizzaBundle\Entity\OrderRepository")
 * @ORM\Table(name="`Order`")
 */
class Order
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="UserOrder", mappedBy="order")
     */
    protected $orders;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $expiration;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $delivery;

    /**
     * @ORM\Column(type="integer")
     */
    protected $capacity;

    /**
     * @ORM\Column(type="integer")
     */
    protected $foreignCapacity;

    /**
     * @ORM\Column(type="datetime") 
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime") 
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $closed;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orders = new \Doctrine\Common\Collections\ArrayCollection();
        $this->createdAt = new \Datetime();
        $this->updatedAt = new \Datetime();
        $this->closed = false;
    }

    /**
     * Get available number of pizzas (virtual)
     */
    public function getAvailableOrders($foreign = false, $noNegative = true) {
        $qty = 0;
        $qtyForeign = 0;
        foreach($this->getOrders() as $uo) {
            if($uo->getPaymentDone()) {
                $qty++;
                if($uo->getForeign())
                    $qtyForeign++;
            }
        }
        $globalCapacity = $this->getCapacity() - $qty;
        $foreignCapacity = $this->getForeignCapacity() - $qtyForeign;

        if($foreign)
            $capacity = min($globalCapacity, $foreignCapacity);
        else
            $capacity = $globalCapacity;

        return $capacity < 0 && $noNegative ? 0 : $capacity;
    }

    /**
     * Get an ordered array of userOrders
     */
    public function getOrdersOrdered() {
        $userOrders = $this->orders->toArray();
        usort($userOrders, array('InsaLan\PizzaBundle\Entity\UserOrder','cmp'));
        return $userOrders;
    }

    /**
     * Date utils
     */
    
    public function isDelivered() {
        return new \Datetime() > $this->delivery;
    }

    public function isExpired() {
        return new \Datetime() > $this->expiration;
    }
    
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
     * Set expiration
     *
     * @param \DateTime $expiration
     * @return Order
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;

        return $this;
    }

    /**
     * Get expiration
     *
     * @return \DateTime
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**cmp
     * Set delivery
     *
     * @param \DateTime $delivery
     * @return Order
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery
     *
     * @return \DateTime
     */
    public function getDelivery()
    {
        return $this->delivery;
    }

    /**
     * Set capacity
     *
     * @param integer $capacity
     * @return Order
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;

        return $this;
    }

    /**
     * Get capacity
     *
     * @return integer
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     * Add orders
     *
     * @param \InsaLan\PizzaBundle\Entity\UserOrder $orders
     * @return Order
     */
    public function addOrder(\InsaLan\PizzaBundle\Entity\UserOrder $orders)
    {
        $this->orders[] = $orders;

        return $this;
    }

    /**
     * Remove orders
     *
     * @param \InsaLan\PizzaBundle\Entity\UserOrder $orders
     */
    public function removeOrder(\InsaLan\PizzaBundle\Entity\UserOrder $orders)
    {
        $this->orders->removeElement($orders);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Order
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Order
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }


    /**
     * Set closed
     *
     * @param boolean $closed
     * @return Order
     */
    public function setClosed($closed)
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * Get closed
     *
     * @return boolean 
     */
    public function getClosed()
    {
        return $this->closed;
    }

    /**
     * Set foreignCapacity
     *
     * @param integer $foreignCapacity
     * @return Order
     */
    public function setForeignCapacity($foreignCapacity)
    {
        $this->foreignCapacity = $foreignCapacity;

        return $this;
    }

    /**
     * Get foreignCapacity
     *
     * @return integer 
     */
    public function getForeignCapacity()
    {
        return $this->foreignCapacity;
    }
}
