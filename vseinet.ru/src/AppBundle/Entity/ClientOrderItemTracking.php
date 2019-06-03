<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ClientOrderItemTracking.
 *
 * @ORM\Table(name="client_order_item_tracking")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ClientOrderItemTrackingRepository")
 */
class ClientOrderItemTracking
{
    /**
     * @var int
     *
     * @ORM\Column(name="order_item_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $orderItemId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expires_at", type="datetime")
     */
    private $expiresAt;

    /**
     * Set orderItemId.
     *
     * @param int $orderItemId
     *
     * @return ClientOrderItemTracking
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
     * Set expiresAt.
     *
     * @param \DateTime $expiresAt
     *
     * @return ClientOrderItemTracking
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt.
     *
     * @return \DateTime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
}
