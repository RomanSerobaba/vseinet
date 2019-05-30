<?php

namespace AppBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class Product
{
    /**
     * @Assert\Type("integer")
     */
    public $baseProductId;

    /**
     * @Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $availability;

    /**
     * @Assert\Type("integer")
     */
    public $supplierId;

    /**
     * @Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $supplierAvailability;

    public function __construct($id, $baseProductId, $availability, $supplierId, $supplierAvailability)
    {
        $this->id = $id;
        $this->baseProductId = $baseProductId;
        $this->availability = $availability;
        $this->supplierId = $supplierId;
        $this->supplierAvailability = $supplierAvailability;
    }
}
