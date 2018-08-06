<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PaymentType
 *
 * @ORM\Table(name="payment_type")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\PaymentTypeRepository")
 */
class PaymentType
{
    const CODE_CASH = 'cash';
    const CODE_CASHLESS = 'cashless';
    const CODE_WEBMONEY = 'webmoney';
    const CODE_BANKCARD = 'bankcard';
    const CODE_SBERBANK = 'sberbank';
    const CODE_CREDIT = 'credit';
    const CODE_INSTALLMENT = 'installment';
    const CODE_TERMINAL = 'terminal';

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
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="abbreviation", type="string", length=255)
     */
    private $abbreviation;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_remote", type="boolean")
     */
    private $isRemote;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_internal", type="boolean", nullable=true)
     */
    private $isInternal;

    /**
     * @var string
     *
     * @ORM\Column(name="cashless_percent", type="decimal", precision=0, scale=0, nullable=true)
     */
    private $cashlessPercent;


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
     * Set code
     *
     * @param string $code
     *
     * @return PaymentType
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PaymentType
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
     * Set abbreviation
     *
     * @param string $abbreviation
     *
     * @return PaymentType
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get abbreviation
     *
     * @return string
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return PaymentType
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
     * Set isRemote
     *
     * @param boolean $isRemote
     *
     * @return PaymentType
     */
    public function setIsRemote($isRemote)
    {
        $this->isRemote = $isRemote;

        return $this;
    }

    /**
     * Get isRemote
     *
     * @return bool
     */
    public function getIsRemote()
    {
        return $this->isRemote;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return PaymentType
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set isInternal
     *
     * @param boolean $isInternal
     *
     * @return PaymentType
     */
    public function setIsInternal($isInternal)
    {
        $this->isInternal = $isInternal;

        return $this;
    }

    /**
     * Get isInternal
     *
     * @return bool
     */
    public function getIsInternal()
    {
        return $this->isInternal;
    }

    /**
     * Set cashlessPercent
     *
     * @param string $cashlessPercent
     *
     * @return PaymentType
     */
    public function setCashlessPercent($cashlessPercent)
    {
        $this->cashlessPercent = $cashlessPercent;

        return $this;
    }

    /**
     * Get cashlessPercent
     *
     * @return string
     */
    public function getCashlessPercent()
    {
        return $this->cashlessPercent;
    }
}

