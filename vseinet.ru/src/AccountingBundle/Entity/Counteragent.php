<?php

namespace AccountingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Counteragent
 *
 * @ORM\Table(name="counteragent")
 * @ORM\Entity(repositoryClass="AccountingBundle\Repository\CounteragentRepository")
 */
class Counteragent
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
     * @var string
     *
     * @ORM\Column(name="tin", type="string", length=255)
     */
    private $tin;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="kpp", type="string", length=255, nullable=true)
     */
    private $kpp;

    /**
     * @var string
     *
     * @ORM\Column(name="okpo", type="string", length=255, nullable=true)
     */
    private $okpo;

    /**
     * @var string
     *
     * @ORM\Column(name="ogrn", type="string", length=255, nullable=true)
     */
    private $ogrn;

    /**
     * @var int
     *
     * @ORM\Column(name="vat_rate", type="integer", nullable=true)
     */
    private $vatRate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="checked_at", type="datetime")
     */
    private $checkedAt;


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
     * Set tin
     *
     * @param string $tin
     *
     * @return Counteragent
     */
    public function setTin($tin)
    {
        $this->tin = $tin;

        return $this;
    }

    /**
     * Get tin
     *
     * @return string
     */
    public function getTin()
    {
        return $this->tin;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Counteragent
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
     * Set kpp
     *
     * @param string $kpp
     *
     * @return Counteragent
     */
    public function setKpp($kpp)
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * Get kpp
     *
     * @return string
     */
    public function getKpp()
    {
        return $this->kpp;
    }

    /**
     * Set okpo
     *
     * @param string $okpo
     *
     * @return Counteragent
     */
    public function setOkpo($okpo)
    {
        $this->okpo = $okpo;

        return $this;
    }

    /**
     * Get okpo
     *
     * @return string
     */
    public function getOkpo()
    {
        return $this->okpo;
    }

    /**
     * Set ogrn
     *
     * @param string $ogrn
     *
     * @return Counteragent
     */
    public function setOgrn($ogrn)
    {
        $this->ogrn = $ogrn;

        return $this;
    }

    /**
     * Get ogrn
     *
     * @return string
     */
    public function getOgrn()
    {
        return $this->ogrn;
    }

    /**
     * Set vatRate
     *
     * @param integer $vatRate
     *
     * @return Counteragent
     */
    public function setVatRate($vatRate)
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    /**
     * Get vatRate
     *
     * @return int
     */
    public function getVatRate()
    {
        return $this->vatRate;
    }

    /**
     * Set checkedAt
     *
     * @param \DateTime $checkedAt
     *
     * @return Counteragent
     */
    public function setCheckedAt($checkedAt)
    {
        $this->checkedAt = $checkedAt;

        return $this;
    }

    /**
     * Get checkedAt
     *
     * @return \DateTime
     */
    public function getCheckedAt()
    {
        return $this->checkedAt;
    }
}

