<?php 

namespace SupplyBundle\Bus\Shipment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SupplierItemsCount
{
    /**
     * @Assert\Type(type="integer")
     */
    public $ourSsellerCounteragentId;

    /**
     * @Assert\Type(type="integer")
     */
    public $count;

    /**
     * SupplierItemsCount constructor.
     *
     * @param $ourSsellerCounteragentId
     * @param $count
     */
    public function __construct($ourSsellerCounteragentId, $count)
    {
        $this->ourSsellerCounteragentId = $ourSsellerCounteragentId;
        $this->cnt = $count;
    }
}