<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Supplier.
 *
 * @ORM\Table(name="supplier")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SupplierRepository")
 */
class Supplier
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
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     */
    private $code;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @var \Date|null
     *
     * @ORM\Column(name="order_delivery_date", type="date", nullable=true)
     */
    private $orderDeliveryDate;

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
     * Set code.
     *
     * @param string $code
     *
     * @return Supplier
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
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return Supplier
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set orderDeliveryDate.
     *
     * @param \Date|null $orderDeliveryDate
     *
     * @return Supplier
     */
    public function setOrderDeliveryDate($orderDeliveryDate = null)
    {
        $this->orderDeliveryDate = $orderDeliveryDate;

        return $this;
    }

    /**
     * Get orderDeliveryDate.
     *
     * @return \Date|null
     */
    public function getOrderDeliveryDate()
    {
        return $this->orderDeliveryDate;
    }
}
