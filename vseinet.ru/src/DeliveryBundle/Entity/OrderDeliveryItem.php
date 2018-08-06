<?php

namespace DeliveryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderDeliveryItem
 *
 * @ORM\Table(name="order_delivery_item")
 * @ORM\Entity(repositoryClass="DeliveryBundle\Repository\OrderDeliveryItemRepository")
 */
class OrderDeliveryItem
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
     * @ORM\Column(name="order_delivery_id", type="integer")
     */
    private $orderDeliveryId;

    /**
     * @var int
     *
     * @ORM\Column(name="order_item_id", type="integer")
     */
    private $orderItemId;

    /**
     * @var int
     *
     * @ORM\Column(name="lifting_cost", type="integer")
     */
    private $liftingCost;

    /**
     * @var json|null
     *
     * @ORM\Column(name="supply_item_ids", type="json", nullable=true)
     */
    private $supplyItemIds;


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
     * Set orderDeliveryId.
     *
     * @param int $orderDeliveryId
     *
     * @return OrderDeliveryItem
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

    /**
     * Set orderItemId.
     *
     * @param int $orderItemId
     *
     * @return OrderDeliveryItem
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
     * Set liftingCost.
     *
     * @param int $liftingCost
     *
     * @return OrderDeliveryItem
     */
    public function setLiftingCost($liftingCost)
    {
        $this->liftingCost = $liftingCost;

        return $this;
    }

    /**
     * Get liftingCost.
     *
     * @return int
     */
    public function getLiftingCost()
    {
        return $this->liftingCost;
    }

    /**
     * Set supplyItemIds.
     *
     * @param json|null $supplyItemIds
     *
     * @return OrderDeliveryItem
     */
    public function setSupplyItemIds($supplyItemIds = null)
    {
        $this->supplyItemIds = $supplyItemIds;

        return $this;
    }

    /**
     * Get supplyItemIds.
     *
     * @return json|null
     */
    public function getSupplyItemIds()
    {
        return $this->supplyItemIds;
    }
}
