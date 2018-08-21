<?php 

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\RepresentativeTypeCode;

class DeliveryTax
{
    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @Enum("AppBundle\Enum\RepresentativeTypeCode")
     */
    public $type;
 
    /**
     * @Assert\Type(type="integer")
     */
    public $geoRegionId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoRegionName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;

    /**
     * @Assert\Type(type="string")
     */
    public $geoCityName;

    /**
     * @Assert\Type(type="string")
     */
    public $address;

    /**
     * @Assert\Type(type="boolean")
     */
    public $hasRetail;

    /**
     * @Assert\Type(type="integer")
     */
    public $deliveryTax;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isOur;


    public function __construct(
        $geoPointId, 
        $type,
        $geoRegionId, 
        $geoRegionName,
        $geoCityId, 
        $geoCityName, 
        $address, 
        $hasRetail,
        $deliveryTax
    ) {
        $this->geoPointId = $geoPointId;
        $this->type = $type;
        $this->geoRegionId = $geoRegionId;
        $this->geoRegionName = $geoRegionName;
        $this->geoCityId = $geoCityId;
        $this->geoCityName = $geoCityName;
        $this->address = $address;
        $this->hasRetail = $hasRetail;
        $this->deliveryTax = $deliveryTax;
        $this->isOur = RepresentativeTypeCode::OUR === $type; 
    }
}
