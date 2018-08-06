<?php 

namespace SupplyBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ReserveOrders
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isNeedCall;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isOrderComments;

    /**
     * @var ReserveOrderItems[]
     *
     * @Assert\Type(type="array<ReserveOrderItems>")
     */
    public $items;

    /**
     * @var ReserveOrderRequests[]
     *
     * @Assert\Type(type="array<ReserveOrderRequests>")
     */
    public $requests;

    /**
     * Order constructor.
     */
    public function __construct()
    {
    }
}