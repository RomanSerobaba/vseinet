<?php

namespace OrderBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrderItemStatus
 *
 * @ORM\Table(name="order_item_status")
 * @ORM\Entity(repositoryClass="OrderBundle\Repository\OrderItemStatusRepository")
 */
class OrderItemStatus
{
    const CODE_LACK = 'lack';
    const CODE_PREPAYABLE = 'prepayable';
    const CODE_CALLABLE = 'callable';
    const CODE_SHIPPING = 'shipping';
    const CODE_TRANSIT = 'transit';
    const CODE_STATIONED = 'stationed';
    const CODE_ARRIVED = 'arrived';
    const CODE_ANNULLED = 'annulled';
    const CODE_CANCELED = 'canceled';
    const CODE_TRANSPORT = 'transport';
    const CODE_RELEASABLE = 'releasable';
    const CODE_COMPLETED = 'completed';
    const CODE_COURIER = 'courier';
    const CODE_ISSUED = 'issued';
    const CODE_REFUNDED = 'refunded';
    const CODE_CREATED = 'created';
    const CODE_POST = 'post';

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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
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
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

