<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientOrder
 *
 * @ORM\Table(name="client_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientOrderRepository")
 */
class ClientOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="order_id", type="integer")
     * @ORM\Id
     */
    private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_type_code", type="string")
     */
    private $paymentTypeCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $userId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="client_counteragent_id", type="integer", nullable=true)
     */
    private $clientCounteragentId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="discount_code_id", type="integer", nullable=true)
     */
    private $discountCodeId;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_type_code", type="string")
     */
    private $deliveryTypeCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="freight_operator_id", type="integer", nullable=true)
     */
    private $freightOperatorId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="geo_address_id", type="integer", nullable=true)
     */
    private $geoAddressId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_call_needed", type="boolean")
     */
    private $isCallNeeded;

    /**
     * @var string|null
     *
     * @ORM\Column(name="call_needed_comment", type="string", nullable=true)
     */
    private $callNeededComment;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_not_reached", type="boolean")
     */
    private $isNotReached;

    /**
     * @var int|null
     *
     * @ORM\Column(name="financial_counteragent_id", type="integer", nullable=true)
     */
    private $financialCounteragentId;


    /**
     * Set orderId.
     *
     * @param int $orderId
     *
     * @return ClientOrder
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId.
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set paymentTypeCode.
     *
     * @param string $paymentTypeCode
     *
     * @return ClientOrder
     */
    public function setPaymentTypeCode($paymentTypeCode)
    {
        $this->paymentTypeCode = $paymentTypeCode;

        return $this;
    }

    /**
     * Get paymentTypeCode.
     *
     * @return string
     */
    public function getPaymentTypeCode()
    {
        return $this->paymentTypeCode;
    }

    /**
     * Set userId.
     *
     * @param int|null $userId
     *
     * @return ClientOrder
     */
    public function setUserId($userId = null)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId.
     *
     * @return int|null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set clientCounteragentId.
     *
     * @param int|null $clientCounteragentId
     *
     * @return ClientOrder
     */
    public function setClientCounteragentId($clientCounteragentId = null)
    {
        $this->clientCounteragentId = $clientCounteragentId;

        return $this;
    }

    /**
     * Get clientCounteragentId.
     *
     * @return int|null
     */
    public function getClientCounteragentId()
    {
        return $this->clientCounteragentId;
    }

    /**
     * Set discountCodeId.
     *
     * @param int|null $discountCodeId
     *
     * @return ClientOrder
     */
    public function setDiscountCodeId($discountCodeId = null)
    {
        $this->discountCodeId = $discountCodeId;

        return $this;
    }

    /**
     * Get discountCodeId.
     *
     * @return int|null
     */
    public function getDiscountCodeId()
    {
        return $this->discountCodeId;
    }

    /**
     * Set deliveryTypeCode.
     *
     * @param string $deliveryTypeCode
     *
     * @return ClientOrder
     */
    public function setDeliveryTypeCode($deliveryTypeCode)
    {
        $this->deliveryTypeCode = $deliveryTypeCode;

        return $this;
    }

    /**
     * Get deliveryTypeCode.
     *
     * @return string
     */
    public function getDeliveryTypeCode()
    {
        return $this->deliveryTypeCode;
    }

    /**
     * Set freightOperatorId.
     *
     * @param int|null $freightOperatorId
     *
     * @return ClientOrder
     */
    public function setFreightOperatorId($freightOperatorId = null)
    {
        $this->freightOperatorId = $freightOperatorId;

        return $this;
    }

    /**
     * Get freightOperatorId.
     *
     * @return int|null
     */
    public function getFreightOperatorId()
    {
        return $this->freightOperatorId;
    }

    /**
     * Set geoAddressId.
     *
     * @param int|null $geoAddressId
     *
     * @return ClientOrder
     */
    public function setGeoAddressId($geoAddressId = null)
    {
        $this->geoAddressId = $geoAddressId;

        return $this;
    }

    /**
     * Get geoAddressId.
     *
     * @return int|null
     */
    public function getGeoAddressId()
    {
        return $this->geoAddressId;
    }

    /**
     * Set isCallNeeded.
     *
     * @param bool $isCallNeeded
     *
     * @return ClientOrder
     */
    public function setIsCallNeeded($isCallNeeded)
    {
        $this->isCallNeeded = $isCallNeeded;

        return $this;
    }

    /**
     * Get isCallNeeded.
     *
     * @return bool
     */
    public function getIsCallNeeded()
    {
        return $this->isCallNeeded;
    }

    /**
     * Set callNeededComment.
     *
     * @param string|null $callNeededComment
     *
     * @return ClientOrder
     */
    public function setCallNeededComment($callNeededComment = null)
    {
        $this->callNeededComment = $callNeededComment;

        return $this;
    }

    /**
     * Get callNeededComment.
     *
     * @return string|null
     */
    public function getCallNeededComment()
    {
        return $this->callNeededComment;
    }

    /**
     * Set isNotReached.
     *
     * @param bool $isNotReached
     *
     * @return ClientOrder
     */
    public function setIsNotReached($isNotReached)
    {
        $this->isNotReached = $isNotReached;

        return $this;
    }

    /**
     * Get isNotReached.
     *
     * @return bool
     */
    public function getIsNotReached()
    {
        return $this->isNotReached;
    }

    /**
     * Set financialCounteragentId.
     *
     * @param int|null $financialCounteragentId
     *
     * @return ClientOrder
     */
    public function setFinancialCounteragentId($financialCounteragentId = null)
    {
        $this->financialCounteragentId = $financialCounteragentId;

        return $this;
    }

    /**
     * Get financialCounteragentId.
     *
     * @return int|null
     */
    public function getFinancialCounteragentId()
    {
        return $this->financialCounteragentId;
    }
}
