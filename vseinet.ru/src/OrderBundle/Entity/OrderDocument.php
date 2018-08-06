<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderDocument
 *
 * @ORM\Table(name="order_document")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrderDocumentRepository")
 */
class OrderDocument
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
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer")
     */
    private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string", length=255, nullable=true)
     */
    private $number;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sent_at", type="datetime", nullable=true)
     */
    private $sentAt;

    /**
     * @var string
     *
     * @ORM\Column(name="contacts", type="string", length=255, nullable=true)
     */
    private $contacts;

    /**
     * @var bool
     *
     * @ORM\Column(name="with_stamp", type="boolean", nullable=true)
     */
    private $withStamp;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_obsolete", type="boolean", nullable=true)
     */
    private $isObsolete;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var int
     *
     * @ORM\Column(name="prepayment_percent", type="integer", nullable=true)
     */
    private $prepaymentPercent;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_type", type="string", length=255, nullable=true)
     */
    private $contractType;


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
     * Set orderId
     *
     * @param integer $orderId
     *
     * @return OrderDocument
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return OrderDocument
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return OrderDocument
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return OrderDocument
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return OrderDocument
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set sentAt
     *
     * @param \DateTime $sentAt
     *
     * @return OrderDocument
     */
    public function setSentAt($sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * Get sentAt
     *
     * @return \DateTime
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Set contacts
     *
     * @param string $contacts
     *
     * @return OrderDocument
     */
    public function setContacts($contacts)
    {
        $this->contacts = $contacts;

        return $this;
    }

    /**
     * Get contacts
     *
     * @return string
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * Set withStamp
     *
     * @param boolean $withStamp
     *
     * @return OrderDocument
     */
    public function setWithStamp($withStamp)
    {
        $this->withStamp = $withStamp;

        return $this;
    }

    /**
     * Get withStamp
     *
     * @return bool
     */
    public function getWithStamp()
    {
        return $this->withStamp;
    }

    /**
     * Set isObsolete
     *
     * @param boolean $isObsolete
     *
     * @return OrderDocument
     */
    public function setIsObsolete($isObsolete)
    {
        $this->isObsolete = $isObsolete;

        return $this;
    }

    /**
     * Get isObsolete
     *
     * @return bool
     */
    public function getIsObsolete()
    {
        return $this->isObsolete;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return OrderDocument
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set prepaymentPercent
     *
     * @param integer $prepaymentPercent
     *
     * @return OrderDocument
     */
    public function setPrepaymentPercent($prepaymentPercent)
    {
        $this->prepaymentPercent = $prepaymentPercent;

        return $this;
    }

    /**
     * Get prepaymentPercent
     *
     * @return int
     */
    public function getPrepaymentPercent()
    {
        return $this->prepaymentPercent;
    }

    /**
     * Set contractType
     *
     * @param string $contractType
     *
     * @return OrderDocument
     */
    public function setContractType($contractType)
    {
        $this->contractType = $contractType;

        return $this;
    }

    /**
     * Get contractType
     *
     * @return string
     */
    public function getContractType()
    {
        return $this->contractType;
    }
}

