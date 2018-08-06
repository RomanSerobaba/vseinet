<?php 

namespace SupplyBundle\Bus\Shipment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SupplierCars
{
    /**
     * @Assert\Type(type="integer")
     */
    public $ourSsellerCounteragentId;

    /**
     * @Assert\Type(type="string")
     */
    public $ourSsellerCounteragentName;

    /**
     * @Assert\Type(type="integer")
     */
    public $count;

    /**
     * SupplierItemsCount constructor.
     *
     * @param $ourSsellerCounteragentId
     * @param $ourSellerCounteragentName
     * @param $count
     */
    public function __construct($ourSsellerCounteragentId, $ourSellerCounteragentName, $count)
    {
        $this->ourSsellerCounteragentId = $ourSsellerCounteragentId;
        $this->ourSellerCounteragentName = $ourSellerCounteragentName;
        $this->cnt = $count;
    }
}