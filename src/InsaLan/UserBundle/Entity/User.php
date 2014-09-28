<?php
namespace InsaLan\UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $credit = 0;

    /**
     * @ORM\Column(name="table_id", type="integer", nullable=true)
     */
    protected $table;

    /**
     * @ORM\Column(name="lol_id", type="integer", nullable=true)
     */
    protected $lol_id;

    /**
     * @ORM\Column(name="lol_name", type="string", length=255, nullable=true)
     */
    protected $lol_name;

    /**
     * @ORM\Column(name="lol_id_validated", type="integer")
     * -1 = Id does not exists
     * 0 = Validated
     * 1 = Error
     * 2 = No information
     */
    protected $lol_id_validated = 2;

    public function __construct()
    {
        parent::__construct();
        // your own logic
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
     * Set credit
     *
     * @param integer $credit
     * @return User
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * Get credit
     *
     * @return integer
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * Set table
     *
     * @param integer $table
     * @return User
     */
    public function setTable($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Get table
     *
     * @return integer
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set lol_id
     *
     * @param integer $lol_id
     * @return User
     */
    public function setLolId($lol_id) {
      $this->lol_id = $lol_id;
      return $this;
    }

    /**
     * Get lol_id
     *
     * @return string
     */
    public function getLolId()
    {
        return $this->lol_id;
    }

    /**
     * Set lol_id_validated
     *
     * @param integer $lol_id_validated
     * @return User
     */
    public function setLolIdValidated($lol_id_validated) {
      $this->lol_id_validated = $lol_id_validated;
      return $this;
    }

    /**
     * Get lol_id_validated
     *
     * @return integer
     */
    public function getLolIdValidated()
    {
        return $this->lol_id_validated;
    }

    /**
     * Set lol_name
     *
     * @param integer $lol_name
     * @return User
     */
    public function setLolName($lol_name) {
      $this->lol_name = $lol_name;
      return $this;
    }

    /**
     * Get lol_name
     *
     * @return string
     */
    public function getLolName()
    {
        return $this->lol_name;
    }
}
