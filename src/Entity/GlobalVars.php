<?php

// NOUVEAU FICHIER TEST DONC PAS FORCEMENT A PRENDRE EN COMPTE


namespace App\Entity;

use App\Repository\GlobalVarsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GlobalVarsRepository::class)
 */
class GlobalVars
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $staffNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lettersNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $romanNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $playersNumber;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $openingDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $openingHour;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $closingDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $closingHour;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $webPrice;

    /**
     * @ORM\Column(type="integer")
     */
    private $campanilePrice;

    /**
     * @ORM\Column(type="integer")
     */
    private $cosplayEdition;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cosplayName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cosplayDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cosplayEndRegistration;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullDates;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $payCheckAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="globalKey", type="string", length=255)
     */
    private $globalKey;

    /**
     * @var string
     *
     * @ORM\Column(name="globalValue", type="string", length=255)
     */
    private $globalValue;

    /**
     * Set globalKey
     *
     * @param string $globalKey
     * @return GlobalVars
     */
    public function setGlobalKey($globalKey)
    {
        $this->globalKey = $globalKey;

        return $this;
    }

    /**
     * Get globalKey
     *
     * @return string
     */
    public function getGlobalKey()
    {
        return $this->globalKey;
    }

    /**
     * Set globalValue
     *
     * @param string $globalValue
     * @return GlobalVars
     */
    public function setGlobalValue($globalValue)
    {
        $this->globalValue = $globalValue;

        return $this;
    }

    /**
     * Get globalValue
     *
     * @return string
     */
    public function getGlobalValue()
    {
        return $this->globalValue;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStaffNumber(): ?int
    {
        return $this->staffNumber;
    }

    public function setStaffNumber(int $staffNumber): self
    {
        $this->staffNumber = $staffNumber;

        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getLettersNumber(): ?string
    {
        return $this->lettersNumber;
    }

    public function setLettersNumber(string $lettersNumber): self
    {
        $this->lettersNumber = $lettersNumber;

        return $this;
    }

    public function getRomanNumber(): ?string
    {
        return $this->romanNumber;
    }

    public function setRomanNumber(string $romanNumber): self
    {
        $this->romanNumber = $romanNumber;

        return $this;
    }

    public function getPlayersNumber(): ?int
    {
        return $this->playersNumber;
    }

    public function setPlayersNumber(int $playersNumber): self
    {
        $this->playersNumber = $playersNumber;

        return $this;
    }

    public function getOpeningDate(): ?string
    {
        return $this->openingDate;
    }

    public function setOpeningDate(string $openingDate): self
    {
        $this->openingDate = $openingDate;

        return $this;
    }

    public function getOpeningHour(): ?string
    {
        return $this->openingHour;
    }

    public function setOpeningHour(string $openingHour): self
    {
        $this->openingHour = $openingHour;

        return $this;
    }

    public function getClosingDate(): ?string
    {
        return $this->closingDate;
    }

    public function setClosingDate(string $closingDate): self
    {
        $this->closingDate = $closingDate;

        return $this;
    }

    public function getClosingHour(): ?string
    {
        return $this->closingHour;
    }

    public function setClosingHour(string $closingHour): self
    {
        $this->closingHour = $closingHour;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getWebPrice(): ?int
    {
        return $this->webPrice;
    }

    public function setWebPrice(int $webPrice): self
    {
        $this->webPrice = $webPrice;

        return $this;
    }

    public function getCampanilePrice(): ?int
    {
        return $this->campanilePrice;
    }

    public function setCampanilePrice(int $campanilePrice): self
    {
        $this->campanilePrice = $campanilePrice;

        return $this;
    }

    public function getCosplayEdition(): ?int
    {
        return $this->cosplayEdition;
    }

    public function setCosplayEdition(int $cosplayEdition): self
    {
        $this->cosplayEdition = $cosplayEdition;

        return $this;
    }

    public function getCosplayName(): ?string
    {
        return $this->cosplayName;
    }

    public function setCosplayName(string $cosplayName): self
    {
        $this->cosplayName = $cosplayName;

        return $this;
    }

    public function getCosplayDate(): ?string
    {
        return $this->cosplayDate;
    }

    public function setCosplayDate(string $cosplayDate): self
    {
        $this->cosplayDate = $cosplayDate;

        return $this;
    }

    public function getCosplayEndRegistration(): ?string
    {
        return $this->cosplayEndRegistration;
    }

    public function setCosplayEndRegistration(string $cosplayEndRegistration): self
    {
        $this->cosplayEndRegistration = $cosplayEndRegistration;

        return $this;
    }

    public function getFullDates(): ?string
    {
        return $this->fullDates;
    }

    public function setFullDates(string $fullDates): self
    {
        $this->fullDates = $fullDates;

        return $this;
    }

    public function getPayCheckAddress(): ?string
    {
        return $this->payCheckAddress;
    }

    public function setPayCheckAddress(string $payCheckAddress): self
    {
        $this->payCheckAddress = $payCheckAddress;

        return $this;
    }
}
