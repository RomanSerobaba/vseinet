<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientOrder.
 *
 * @ORM\Table(name="client_order")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientOrderRepository")
 */
class ClientOrder
{
    /**
     * @var int
     *
     * @ORM\Column(name="order_did", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_type_code", type="string", length=30)
     */
    public $deliveryTypeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_type_code", type="string", length=30)
     */
    public $paymentTypeCode;

    /**
     * Get orderId.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set deliveryTypeCode.
     *
     * @param string $deliveryTypeCode
     *
     * @return OrderDoc
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
     * Set paymentTypeCode.
     *
     * @param string $paymentTypeCode
     *
     * @return OrderDoc
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
}
