<?php
namespace InsaLan\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\ArrayObject;
use InsaLan\UserBundle\Entity\User;

/**
 * @ORM\Table(name="payum_payment_details")
 * @ORM\Entity
 */
class PaymentDetails extends ArrayObject
{

    // Available payment types
    const TYPE_UNDEFINED = 0;
    const TYPE_PAYPAL = 1;
    const TYPE_CB = 2;
    const TYPE_CHECK = 3;
    const TYPE_CASH = 4;

    // Where payment has been made
    const PLACE_UNDEFINED = 0;
    const PLACE_WEB = 1;
    const PLACE_ON_SITE = 2;
    const PLACE_IN_PARTNER_SHOP = 3;


    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="InsaLan\UserBundle\Entity\User")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="InsaLan\UserBundle\Entity\Discount")
     */
    protected $discount;

    /**
     * @ORM\Column(type="integer")
     */
    protected $place = self::PLACE_UNDEFINED;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type = self::TYPE_UNDEFINED;

    /**
     * @ORM\Column(type="float")
     * Price without any discount
     */
    protected $rawPrice;


    private $detailId = 0;

    public function addPaymentDetail($name, $price, $description) {
        $i = $this->detailId++;
        $this["L_PAYMENTREQUEST_0_NAME$i"] = $name;
        $this["L_PAYMENTREQUEST_0_AMT$i"] = $price;
        $this["L_PAYMENTREQUEST_0_DESC$i"] = $description;
        $this["L_PAYMENTREQUEST_0_NUMBER$i"] = 1;
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
     * Set user
     *
     * @param \InsaLan\UserBundle\Entity\User $user
     * @return PaymentDetails
     */
    public function setUser(\InsaLan\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;
        $this["INVNUM"] = $user->getId();

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
     * Set discount
     *
     * @param \InsaLan\UserBundle\Entity\Discount $discount
     * @return PaymentDetails
     */
    public function setDiscount(\InsaLan\UserBundle\Entity\Discount $discount = null)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return \InsaLan\UserBundle\Entity\Discount
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set place
     *
     * @param integer $place
     * @return PaymentDetails
     */
    public function setPlace($place)
    {
        $this->place = $place;

        return $this;
    }

    /**
     * Get place
     *
     * @return integer
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return PaymentDetails
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
     * Set rawPrice
     *
     * @param float $rawPrice
     * @return PaymentDetails
     */
    public function setRawPrice($rawPrice)
    {
        $this->rawPrice = $rawPrice;

        return $this;
    }

    /**
     * Get rawPrice
     *
     * @return float
     */
    public function getRawPrice()
    {
        return $this->rawPrice;
    }

    /**
     * Get details
     *
     * @return Array
     */
    public function getDetails()
    {
        return $this->details;
    }
}
