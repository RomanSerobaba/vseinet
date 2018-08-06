<?php 

namespace SupplyBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Reserve
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $phone;

    /**
     * @Assert\Type(type="string")
     */
    public $email;

    /**
     * @Assert\Type(type="string")
     */
    public $owner;

    /**
     * @var ReserveOrders[]
     *
     * @Assert\Type(type="array<ReserveOrders>")
     */
    public $orders;

    /**
     * Order constructor.
     */
    public function __construct()
    {
    }
}