<?php

namespace DeliveryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeliveryItem
 *
 * @ORM\Table(name="delivery_item")
 * @ORM\Entity(repositoryClass="DeliveryBundle\Repository\DeliveryItemRepository")
 */
class DeliveryItem
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
     * @ORM\Column(name="delivery_id", type="integer")
     */
    private $deliveryId;

    /**
     * @var int
     *
     * @ORM\Column(name="order_delivery_id", type="integer")
     */
    private $orderDeliveryId;


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
     * Set deliveryId.
     *
     * @param int $deliveryId
     *
     * @return DeliveryItem
     */
    public function setDeliveryId($deliveryId)
    {
        $this->deliveryId = $deliveryId;

        return $this;
    }

    /**
     * Get deliveryId.
     *
     * @return int
     */
    public function getDeliveryId()
    {
        return $this->deliveryId;
    }

    /**
     * Set orderDeliveryId.
     *
     * @param int $orderDeliveryId
     *
     * @return DeliveryItem
     */
    public function setOrderDeliveryId($orderDeliveryId)
    {
        $this->orderDeliveryId = $orderDeliveryId;

        return $this;
    }

    /**
     * Get orderDeliveryId.
     *
     * @return int
     */
    public function getOrderDeliveryId()
    {
        return $this->orderDeliveryId;
    }
}
