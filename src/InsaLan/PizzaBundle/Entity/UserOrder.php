<?php
namespace InsaLan\PizzaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="InsaLan\PizzaBundle\Entity\UserOrderRepository")
 */
class UserOrder
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="InsaLan\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="orders")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="InsaLan\PizzaBundle\Entity\Pizza")
     * @ORM\JoinColumn(name="pizza_id", referencedColumnName="id")
     */
    protected $pizza;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $delivered = false;

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
     * Set delivered
     *
     * @param boolean $delivered
     * @return UserOrder
     */
    public function setDelivered($delivered)
    {
        $this->delivered = $delivered;

        return $this;
    }

    /**
     * Get delivered
     *
     * @return boolean
     */
    public function getDelivered()
    {
        return $this->delivered;
    }

    /**
     * Set user
     *
     * @param \InsaLan\UserBundle\Entity\User $user
     * @return UserOrder
     */
    public function setUser(\InsaLan\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \InsaLan\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set order
     *
     * @param \InsaLan\PizzaBundle\Entity\Order $order
     * @return UserOrder
     */
    public function setOrder(\InsaLan\PizzaBundle\Entity\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \InsaLan\PizzaBundle\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set pizza
     *
     * @param \InsaLan\PizzaBundle\Entity\Pizza $pizza
     * @return UserOrder
     */
    public function setPizza(\InsaLan\PizzaBundle\Entity\Pizza $pizza = null)
    {
        $this->pizza = $pizza;

        return $this;
    }

    /**
     * Get pizza
     *
     * @return \InsaLan\PizzaBundle\Entity\Pizza
     */
    public function getPizza()
    {
        return $this->pizza;
    }
}
