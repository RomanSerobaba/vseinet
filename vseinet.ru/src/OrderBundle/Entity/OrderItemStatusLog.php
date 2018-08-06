<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItemStatusLog
 *
 * @ORM\Table(name="order_item_status_log")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrderItemStatusLogRepository")
 */
class OrderItemStatusLog
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
     * @var string
     *
     * @ORM\Column(name="order_item_status_code", type="string", length=255)
     */
    private $orderItemStatusCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var int|null
     *
     * @ORM\Column(name="updated_by", type="integer", nullable=true)
     */
    private $updatedBy;


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
     * @return OrderItemStatusLog
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
     * Set orderItemStatusCode.
     *
     * @param string $orderItemStatusCode
     *
     * @return OrderItemStatusLog
     */
    public function setOrderItemStatusCode($orderItemStatusCode)
    {
        $this->orderItemStatusCode = $orderItemStatusCode;

        return $this;
    }

    /**
     * Get orderItemStatusCode.
     *
     * @return string
     */
    public function getOrderItemStatusCode()
    {
        return $this->orderItemStatusCode;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return OrderItemStatusLog
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set updatedBy.
     *
     * @param int|null $updatedBy
     *
     * @return OrderItemStatusLog
     */
    public function setUpdatedBy($updatedBy = null)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy.
     *
     * @return int|null
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
}
