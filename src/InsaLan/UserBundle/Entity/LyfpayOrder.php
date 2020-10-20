<?php
namespace InsaLan\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table()
 */
class LyfpayOrder extends BaseOrder
{
    /**
     * @ORM\Column(type="string", length=80, unique=true)
     */
    protected $shopReference;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->shopReference = 'ref' . $this->id;
    }

    /**
     * Get shopReference
     *
     * @return string
     */
    public function getShopReference()
    {
        return $this->shopReference;
    }

}
