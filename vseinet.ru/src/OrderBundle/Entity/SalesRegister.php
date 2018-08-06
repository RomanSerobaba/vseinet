<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SalesRegister
 *
 * @ORM\Table(name="sales_register")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\SalesRegisterRepository")
 */
class SalesRegister
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
     * @ORM\Column(name="order_item_id", type="integer")
     */
    private $orderItemId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="supply_item_id", type="integer", nullable=true)
     */
    private $supplyItemId;

    /**
     * @var int
     *
     * @ORM\Column(name="delta", type="integer")
     */
    private $delta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_at", type="datetime")
     */
    private $registeredAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="registrator_id", type="integer", nullable=true)
     */
    private $registratorId;

    /**
     * @var string
     *
     * @ORM\Column(name="registrator_type_code", type="string", length=255)
     */
    private $registratorTypeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="register_operation_type_code", type="string", length=255)
     */
    private $registerOperationTypeCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="created_by", type="integer", nullable=true)
     */
    private $createdBy;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set orderItemId.
     *
     * @param int $orderItemId
     *
     * @return SalesRegister
     */
    public function setOrderItemId($orderItemId)
    {
        $this->orderItemId = $orderItemId;

        return $this;
    }

    /**
     * Get orderItemId.
     *
     * @return int
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * Set supplyItemId.
     *
     * @param int|null $supplyItemId
     *
     * @return SalesRegister
     */
    public function setSupplyItemId($supplyItemId = null)
    {
        $this->supplyItemId = $supplyItemId;

        return $this;
    }

    /**
     * Get supplyItemId.
     *
     * @return int|null
     */
    public function getSupplyItemId()
    {
        return $this->supplyItemId;
    }

    /**
     * Set delta.
     *
     * @param int $delta
     *
     * @return SalesRegister
     */
    public function setDelta($delta)
    {
        $this->delta = $delta;

        return $this;
    }

    /**
     * Get delta.
     *
     * @return int
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * Set registeredAt.
     *
     * @param \DateTime $registeredAt
     *
     * @return SalesRegister
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    /**
     * Get registeredAt.
     *
     * @return \DateTime
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }

    /**
     * Set registratorId.
     *
     * @param int|null $registratorId
     *
     * @return SalesRegister
     */
    public function setRegistratorId($registratorId = null)
    {
        $this->registratorId = $registratorId;

        return $this;
    }

    /**
     * Get registratorId.
     *
     * @return int|null
     */
    public function getRegistratorId()
    {
        return $this->registratorId;
    }

    /**
     * Set registratorTypeCode.
     *
     * @param string $registratorTypeCode
     *
     * @return SalesRegister
     */
    public function setRegistratorTypeCode($registratorTypeCode)
    {
        $this->registratorTypeCode = $registratorTypeCode;

        return $this;
    }

    /**
     * Get registratorTypeCode.
     *
     * @return string
     */
    public function getRegistratorTypeCode()
    {
        return $this->registratorTypeCode;
    }

    /**
     * Set registerOperationTypeCode.
     *
     * @param string $registerOperationTypeCode
     *
     * @return SalesRegister
     */
    public function setRegisterOperationTypeCode($registerOperationTypeCode)
    {
        $this->registerOperationTypeCode = $registerOperationTypeCode;

        return $this;
    }

    /**
     * Get registerOperationTypeCode.
     *
     * @return string
     */
    public function getRegisterOperationTypeCode()
    {
        return $this->registerOperationTypeCode;
    }

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return SalesRegister
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy.
     *
     * @param int|null $createdBy
     *
     * @return SalesRegister
     */
    public function setCreatedBy($createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy.
     *
     * @return int|null
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
