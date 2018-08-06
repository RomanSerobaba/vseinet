<?php

namespace RegisterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GoodsNeedRegister
 *
 * @ORM\Table(name="goods_need_register")
 * @ORM\Entity(repositoryClass="RegisterBundle\Repository\GoodsNeedRegisterRepository")
 */
class GoodsNeedRegister
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
     * @ORM\Column(name="delta", type="integer")
     */
    private $delta;

    /**
     * @var int|null
     *
     * @ORM\Column(name="order_item_id", type="integer", nullable=true)
     */
    private $orderItemId;

    /**
     * @var int
     *
     * @ORM\Column(name="registrator_id", type="integer")
     */
    private $registratorId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="registered_at", type="datetime")
     */
    private $registeredAt;

    /**
     * @var string
     *
     * @ORM\Column(name="register_operation_type_code", type="string", length=255)
     */
    private $registerOperationTypeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="registrator_type_code", type="string", length=255)
     */
    private $registratorTypeCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="base_product_id", type="integer", nullable=true)
     */
    private $baseProductId;


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
     * Set delta.
     *
     * @param int $delta
     *
     * @return GoodsNeedRegister
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
     * Set orderItemId.
     *
     * @param int|null $orderItemId
     *
     * @return GoodsNeedRegister
     */
    public function setOrderItemId($orderItemId = null)
    {
        $this->orderItemId = $orderItemId;

        return $this;
    }

    /**
     * Get orderItemId.
     *
     * @return int|null
     */
    public function getOrderItemId()
    {
        return $this->orderItemId;
    }

    /**
     * Set registratorId.
     *
     * @param int $registratorId
     *
     * @return GoodsNeedRegister
     */
    public function setRegistratorId($registratorId)
    {
        $this->registratorId = $registratorId;

        return $this;
    }

    /**
     * Get registratorId.
     *
     * @return int
     */
    public function getRegistratorId()
    {
        return $this->registratorId;
    }

    /**
     * Set registeredAt.
     *
     * @param \DateTime $registeredAt
     *
     * @return GoodsNeedRegister
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
     * Set registerOperationTypeCode.
     *
     * @param string $registerOperationTypeCode
     *
     * @return GoodsNeedRegister
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
     * Set registratorTypeCode.
     *
     * @param string $registratorTypeCode
     *
     * @return GoodsNeedRegister
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
     * Set baseProductId.
     *
     * @param int|null $baseProductId
     *
     * @return GoodsNeedRegister
     */
    public function setBaseProductId($baseProductId = null)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId.
     *
     * @return int|null
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }
}
