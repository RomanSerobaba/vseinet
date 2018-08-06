<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderAnnulItem
 *
 * @ORM\Table(name="order_annul_item")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrderAnnulItemRepository")
 */
class OrderAnnulItem
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
     * @var int
     *
     * @ORM\Column(name="order_annul_id", type="integer")
     */
    private $orderAnnulId;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="string", length=255)
     */
    private $quantity;


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
     * @return OrderAnnulItem
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
     * Set orderAnnulId.
     *
     * @param int $orderAnnulId
     *
     * @return OrderAnnulItem
     */
    public function setOrderAnnulId($orderAnnulId)
    {
        $this->orderAnnulId = $orderAnnulId;

        return $this;
    }

    /**
     * Get orderAnnulId.
     *
     * @return int
     */
    public function getOrderAnnulId()
    {
        return $this->orderAnnulId;
    }

    /**
     * Set quantity.
     *
     * @param string $quantity
     *
     * @return OrderAnnulItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity.
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
