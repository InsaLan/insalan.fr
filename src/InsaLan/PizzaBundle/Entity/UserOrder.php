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

    const TYPE_MANUAL = 0;
    const TYPE_PAYPAL = 1;

    public static function cmp($a, $b)
    {
        return strcasecmp($a->getUsername(), $b->getUsername());
    }

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
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fullnameCanonical; // for manual orders

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $usernameCanonical; // for manual orders

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
     * @ORM\Column(type="integer")
     */
    protected $type;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $paymentDone;

    /**
     * @ORM\Column(type="datetime") 
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime") 
     */
    protected $updatedAt;
        
    public function __construct() {
        $this->paymentDone = false;
        $this->createdAt = $this->updatedAt = new \DateTime();
    }

    public function getUsername() {
        if($this->user)
            return $this->user->getUsername();
        else
            return $this->usernameCanonical;
    }

    public function getFullname() {
        if($this->user)
            return $this->user->getFirstName() . " " . $this->user->getLastName();
        else
            return $this->fullnameCanonical;
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


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UserOrder
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
     * @return UserOrder
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
     * Set type
     *
     * @param integer $type
     * @return UserOrder
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set paymentDone
     *
     * @param boolean $paymentDone
     * @return UserOrder
     */
    public function setPaymentDone($paymentDone)
    {
        $this->paymentDone = $paymentDone;

        return $this;
    }

    /**
     * Get paymentDone
     *
     * @return boolean 
     */
    public function getPaymentDone()
    {
        return $this->paymentDone;
    }

    /**
     * Set fullnameCanonical
     *
     * @param string $fullnameCanonical
     * @return UserOrder
     */
    public function setFullnameCanonical($fullnameCanonical)
    {
        $this->fullnameCanonical = $fullnameCanonical;

        return $this;
    }

    /**
     * Get fullnameCanonical
     *
     * @return string 
     */
    public function getFullnameCanonical()
    {
        return $this->fullnameCanonical;
    }

    /**
     * Set usernameCanonical
     *
     * @param string $usernameCanonical
     * @return UserOrder
     */
    public function setUsernameCanonical($usernameCanonical)
    {
        $this->usernameCanonical = $usernameCanonical;

        return $this;
    }

    /**
     * Get usernameCanonical
     *
     * @return string 
     */
    public function getUsernameCanonical()
    {
        return $this->usernameCanonical;
    }
}
