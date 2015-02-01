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
}
