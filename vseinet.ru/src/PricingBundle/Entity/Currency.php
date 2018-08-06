<?php

namespace PricingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Currency
 *
 * @ORM\Table(name="currency")
 * @ORM\Entity(repositoryClass="PricingBundle\Repository\CurrencyRepository")
 */
class Currency
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="usd", type="decimal", precision=10, scale=2)
     */
    private $usd;

    /**
     * @var string
     *
     * @ORM\Column(name="eur", type="decimal", precision=10, scale=2)
     */
    private $eur;

    /**
     * @var string
     *
     * @ORM\Column(name="gbr", type="decimal", precision=10, scale=2)
     */
    private $gbr;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Currency
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set usd
     *
     * @param string $usd
     *
     * @return Currency
     */
    public function setUsd($usd)
    {
        $this->usd = $usd;

        return $this;
    }

    /**
     * Get usd
     *
     * @return string
     */
    public function getUsd()
    {
        return $this->usd;
    }

    /**
     * Set eur
     *
     * @param string $eur
     *
     * @return Currency
     */
    public function setEur($eur)
    {
        $this->eur = $eur;

        return $this;
    }

    /**
     * Get eur
     *
     * @return string
     */
    public function getEur()
    {
        return $this->eur;
    }

    /**
     * Set gbr
     *
     * @param string $gbr
     *
     * @return Currency
     */
    public function setGbr($gbr)
    {
        $this->gbr = $gbr;

        return $this;
    }

    /**
     * Get gbr
     *
     * @return string
     */
    public function getGbr()
    {
        return $this->gbr;
    }

    /**
     * Convert price
     * 
     * @param int $price    
     * @param string $currency 
     * 
     * @return int
     */
    public function convert($price, $currency)
    {
        switch (strtoupper($currency)) {
            case 'USD':
                $k = 1.35;
                $rate = $this->getUsd();
                break;

            case 'EUR':
                $k = 1.35;
                $rate = $this->getEur();
                break;

            case 'GBR':
                $k = 1.35;
                $rate = $this->getGbr();
                break;

            default:
                throw new \RuntimeException(sprintf('Валюта %s не определена', $currency));
        }

        return round($price * $k * $rate);
    }
}