<?php
/**
 * Copyright (c) VseInet.ru
 * Author: Kalchenko Sergey
 * Date: 13.03.2019.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Counteragent.
 *
 * @ORM\Table(name="counteragent")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CounteragentRepository")
 */
class Counteragent
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(name="tin", type="string", length=255)
     *
     * @var string
     */
    private $tin;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="kpp", type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $kpp;

    /**
     * @ORM\Column(name="okpo", type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $okpo;

    /**
     * @ORM\Column(name="ogrn", type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $ogrn;

    /**
     * @ORM\Column(name="vat_rate", type="integer", nullable=true)
     *
     * @var int
     */
    private $vatRate;

    /**
     * @ORM\Column(name="checked_at", type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $checkedAt;

    /**
     * @ORM\Column(name="legal_address_id", type="integer", nullable=true)
     *
     * @var int
     */
    private $legalAddressId;

    /**
     * @ORM\Column(name="delivery_address_id", type="integer", nullable=true)
     *
     * @var int
     */
    private $deliveryAddressId;

    /**
     * @ORM\Column(name="correspondent_address_id", type="integer", nullable=true)
     *
     * @var int
     */
    private $correspondentAddressId;

    /**
     * @ORM\Column(name="phone_id", type="integer", nullable=true)
     *
     * @var int
     */
    private $phoneId;

    /**
     * @ORM\Column(name="email_id", type="integer", nullable=true)
     *
     * @var int
     */
    private $emailId;

    /**
     * @ORM\Column(name="agent_name_short", type="string", nullable=true)
     *
     * @var string
     */
    private $agentNameShort;

    /**
     * @ORM\Column(name="agent_name_declension", type="string", nullable=true)
     *
     * @var string
     */
    private $agentNameDeclension;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set tin.
     *
     * @param string $tin
     *
     * @return $this
     */
    public function setTin($tin): self
    {
        $this->tin = $tin;

        return $this;
    }

    /**
     * Get tin.
     *
     * @return string
     */
    public function getTin(): string
    {
        return $this->tin;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set kpp.
     *
     * @param string|null $kpp
     *
     * @return $this
     */
    public function setKpp(?string $kpp = null): self
    {
        $this->kpp = $kpp;

        return $this;
    }

    /**
     * Get kpp.
     *
     * @return string|null
     */
    public function getKpp(): ?string
    {
        return $this->kpp;
    }

    /**
     * Set okpo.
     *
     * @param string|null $okpo
     *
     * @return $this
     */
    public function setOkpo(?string $okpo = null): self
    {
        $this->okpo = $okpo;

        return $this;
    }

    /**
     * Get okpo.
     *
     * @return string|null
     */
    public function getOkpo(): ?string
    {
        return $this->okpo;
    }

    /**
     * Set ogrn.
     *
     * @param string|null $ogrn
     *
     * @return $this
     */
    public function setOgrn(?string $ogrn = null): self
    {
        $this->ogrn = $ogrn;

        return $this;
    }

    /**
     * Get ogrn.
     *
     * @return string|null
     */
    public function getOgrn(): ?string
    {
        return $this->ogrn;
    }

    /**
     * Set vatRate.
     *
     * @param int|null $vatRate
     *
     * @return $this
     */
    public function setVatRate(?int $vatRate = null): self
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    /**
     * Get vatRate.
     *
     * @return int|null
     */
    public function getVatRate(): ?int
    {
        return $this->vatRate;
    }

    /**
     * Set checkedAt.
     *
     * @param \DateTime|null $checkedAt
     *
     * @return $this
     */
    public function setCheckedAt(?\DateTime $checkedAt = null): self
    {
        $this->checkedAt = $checkedAt;

        return $this;
    }

    /**
     * Get checkedAt.
     *
     * @return \DateTime|null
     */
    public function getCheckedAt(): ?\DateTime
    {
        return $this->checkedAt;
    }

    /**
     * Set legalAddressId.
     *
     * @param int|null $legalAddressId
     *
     * @return $this
     */
    public function setLegalAddressId(?int $legalAddressId = null): self
    {
        $this->legalAddressId = $legalAddressId;

        return $this;
    }

    /**
     * Get legalAddressId.
     *
     * @return int|null
     */
    public function getLegalAddressId(): ?int
    {
        return $this->legalAddressId;
    }

    /**
     * Set deliveryAddressId.
     *
     * @param int|null $deliveryAddressId
     *
     * @return $this
     */
    public function setDeliveryAddressId(?int $deliveryAddressId = null): self
    {
        $this->deliveryAddressId = $deliveryAddressId;

        return $this;
    }

    /**
     * Get deliveryAddressId.
     *
     * @return int|null
     */
    public function getDeliveryAddressId(): ?int
    {
        return $this->deliveryAddressId;
    }

    /**
     * Set correspondentAddressId.
     *
     * @param int|null $correspondentAddressId
     *
     * @return $this
     */
    public function setCorrespondentAddressId(?int $correspondentAddressId = null): self
    {
        $this->correspondentAddressId = $correspondentAddressId;

        return $this;
    }

    /**
     * Get correspondentAddressId.
     *
     * @return int|null
     */
    public function getCorrespondentAddressId(): ?int
    {
        return $this->correspondentAddressId;
    }

    /**
     * Set phoneId.
     *
     * @param int|null $phoneId
     *
     * @return $this
     */
    public function setPhoneId(?int $phoneId = null): self
    {
        $this->phoneId = $phoneId;

        return $this;
    }

    /**
     * Get phoneId.
     *
     * @return int|null
     */
    public function getPhoneId(): ?int
    {
        return $this->phoneId;
    }

    /**
     * Set emailId.
     *
     * @param int|null $emailId
     *
     * @return $this
     */
    public function setEmailId(?int $emailId = null): self
    {
        $this->emailId = $emailId;

        return $this;
    }

    /**
     * Get emailId.
     *
     * @return int|null
     */
    public function getEmailId(): ?int
    {
        return $this->emailId;
    }

    /**
     * Set agentNameShort.
     *
     * @param string|null $agentNameShort
     *
     * @return $this
     */
    public function setAgentNameShort(?string $agentNameShort = null): self
    {
        $this->agentNameShort = $agentNameShort;

        return $this;
    }

    /**
     * Get agentNameShort.
     *
     * @return string|null
     */
    public function getAgentNameShort(): ?string
    {
        return $this->agentNameShort;
    }

    /**
     * Set agentNameDeclension.
     *
     * @param string|null $agentNameDeclension
     *
     * @return $this
     */
    public function setAgentNameDeclension(?string $agentNameDeclension = null): self
    {
        $this->agentNameDeclension = $agentNameDeclension;

        return $this;
    }

    /**
     * Get agentNameDeclension.
     *
     * @return string|null
     */
    public function getAgentNameDeclension(): ?string
    {
        return $this->agentNameDeclension;
    }
}
