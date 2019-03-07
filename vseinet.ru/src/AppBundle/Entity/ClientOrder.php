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
     * Get orderId.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
