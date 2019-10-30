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

    /**
     * @Assert\Date
     */
    public $supplierDeliveryDate;

    /**
     * @Assert\Type("integer")
     */
    public $geoPointId;

    /**
     * @Assert\All(
     *  @Assert\Type("AppBundle\Bus\Product\Query\DTO\FreeReserve")
     * )
     */
    public $reserves;

    public function __construct($id, $baseProductId, $availability, $supplierId, $supplierAvailability, $supplierDeliveryDate = null, $geoPointId = null)
    {
        $this->id = $id;
        $this->baseProductId = $baseProductId;
        $this->availability = $availability;
        $this->supplierId = $supplierId;
        $this->supplierAvailability = $supplierAvailability;
        $this->supplierDeliveryDate = $supplierDeliveryDate;
        $this->geoPointId = $geoPointId;
    }
}
