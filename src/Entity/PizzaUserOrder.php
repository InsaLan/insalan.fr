<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PizzaUserOrderRepository")
 */
class PizzaUserOrder
{

    const PAYPAL_INCREASE = 1; // EUR

    const TYPE_MANUAL = 0;
    const TYPE_PAYPAL = 1;

    const FULL_PRICE  = 0;
    const STAFF_PRICE = 1;
    const FREE_PRICE  = 2;

    public static function cmp($a, $b)
    {
        $c = strcasecmp($a->getUsername(), $b->getUsername());
        if($c === 0) {
            $c = strcasecmp($a->getFullname(), $b->getFullname());
        }
        return $c;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
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
     * @ORM\ManyToOne(targetEntity="PizzaOrder", inversedBy="orders")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    protected $order;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pizza")
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
     * @ORM\Column(type="integer")
     */
    protected $price;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $paymentDone;

    /**
     * @ORM\Column(type="boolean", name="foreign_user")
     */
    protected $foreign;

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
        $this->price = self::FULL_PRICE;
        $this->foreign = false;
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

    public function __toString() {
        return "Commande #" . $this->getId();
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
     * @return PizzaUserOrder
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
     * @param User $user
     * @return PizzaUserOrder
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set order
     *
     * @param \App\Entity\PizzaOrder $order
     * @return PizzaUserOrder
     */
    public function setOrder(\App\Entity\PizzaOrder $order = null)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return \App\Entity\PizzaOrder
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set pizza
     *
     * @param \App\Entity\Pizza $pizza
     * @return PizzaUserOrder
     */
    public function setPizza(\App\Entity\Pizza $pizza = null)
    {
        $this->pizza = $pizza;

        return $this;
    }

    /**
     * Get pizza
     *
     * @return \App\Entity\Pizza
     */
    public function getPizza()
    {
        return $this->pizza;
    }


    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return PizzaUserOrder
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
     * @return PizzaUserOrder
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
     * @return PizzaUserOrder
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
     * @return PizzaUserOrder
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
     * @return PizzaUserOrder
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
     * @return PizzaUserOrder
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

    /**
     * Set price
     *
     * @param integer $price
     * @return PizzaUserOrder
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return integer
     */
    public function getPrice()
    {
        return $this->price;
    }


    /**
     * Set foreign
     *
     * @param boolean $foreign
     * @return PizzaUserOrder
     */
    public function setForeign($foreign)
    {
        $this->foreign = $foreign;

        return $this;
    }

    /**
     * Get foreign
     *
     * @return boolean
     */
    public function getForeign()
    {
        return $this->foreign;
    }
}
