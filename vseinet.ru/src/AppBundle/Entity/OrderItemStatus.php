<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItemStatus.
 *
 * @ORM\Table(name="order_item_status")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrderItemStatusRepository")
 */
class OrderItemStatus
{
    public const CODE_LACK = 'lack';
    public const CODE_PREPAYABLE = 'prepayable';
    public const CODE_CALLABLE = 'callable';
    public const CODE_SHIPPING = 'shipping';
    public const CODE_TRANSIT = 'transit';
    public const CODE_STATIONED = 'stationed';
    public const CODE_ARRIVED = 'arrived';
    public const CODE_ANNULLED = 'annulled';
    public const CODE_CANCELED = 'canceled';
    public const CODE_TRANSPORT = 'transport';
    public const CODE_RELEASABLE = 'releasable';
    public const CODE_COMPLETED = 'completed';
    public const CODE_COURIER = 'courier';
    public const CODE_ISSUED = 'issued';
    public const CODE_REFUNDED = 'refunded';
    public const CODE_CREATED = 'created';
    public const CODE_POST = 'post';

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="code", type="string", length=128)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="client_name", type="string", length=255)
     */
    private $clientName;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_final", type="boolean")
     */
    private $isFinal;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_basic", type="boolean")
     */
    private $isBasic;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;

    /**
     * Set code.
     *
     * @param string $code
     *
     * @return OrderItemStatus
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return OrderItemStatus
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set clientName.
     *
     * @param string $clientName
     *
     * @return OrderItemStatus
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * Get clientName.
     *
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * Set isFinal.
     *
     * @param string $isFinal
     *
     * @return OrderItemStatus
     */
    public function setIsFinal($isFinal)
    {
        $this->isFinal = $isFinal;

        return $this;
    }

    /**
     * Get isFinal.
     *
     * @return string
     */
    public function getIsFinal()
    {
        return $this->isFinal;
    }

    /**
     * Set isBasic.
     *
     * @param string $isBasic
     *
     * @return OrderItemStatus
     */
    public function setIsBasic($isBasic)
    {
        $this->isBasic = $isBasic;

        return $this;
    }

    /**
     * Get isBasic.
     *
     * @return string
     */
    public function getIsBasic()
    {
        return $this->isBasic;
    }

    /**
     * Set sortOrder.
     *
     * @param int|null $sortOrder
     *
     * @return OrderItemStatus
     */
    public function setSortOrder($sortOrder = null)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder.
     *
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}
