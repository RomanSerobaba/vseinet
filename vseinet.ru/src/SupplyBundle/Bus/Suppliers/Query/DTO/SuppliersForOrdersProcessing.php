<?php 

namespace SupplyBundle\Bus\Suppliers\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SuppliersForOrdersProcessing
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $managerId;

    /**
     * @Assert\Type(type="integer")
     */
    public $processingItemsQuantity;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierReserveId;
}