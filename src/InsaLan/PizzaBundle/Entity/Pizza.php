<?php
namespace InsaLan\PizzaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class Pizza
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @ORM\Column(type="float")
     */
    protected $price;

    /**
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $veggie;


    public function __toString() {
      if ($this->getName()) {
        return $this->getName();
      }
      // We have to return a string value
      return '';
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
     * Set name
     *
     * @param string $name
     * @return Pizza
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Pizza
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Pizza
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set veggie
     *
     * @param boolean $veggie
     * @return Order
     */
    public function setVeggie($veggie)
    {
        $this->veggie = $veggie;

        return $this;
    }

    /**
     * Get veggie
     *
     * @return boolean
     */
    public function getVeggie()
    {
        return $this->veggie;
    }

}
