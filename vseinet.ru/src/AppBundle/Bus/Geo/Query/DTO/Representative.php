<?php 

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\RepresentativeTypeCode;

class Representative
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
     * @Assert\Type(type="boolean")
     */
    public $hasDelivery;

    /**
     * @Assert\Type(type="integer")
     */
    public $deliveryTax;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isOur;

    /**
     * @Assert\Type(type="string")
     */
    public $phone;

    /**
     * @Assert\Type(type="AppBundle\Bus\Geo\Query\DTO\Schedule")
     */
    public $schedule;


    public function __construct(
        $geoPointId, 
        $type,
        $geoRegionId, 
        $geoRegionName,
        $geoCityId, 
        $geoCityName, 
        $address, 
        $hasRetail,
        $hasDelivery,
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
        $this->hasDelivery = $hasDelivery;
        $this->deliveryTax = $deliveryTax;
        $this->isOur = RepresentativeTypeCode::OUR === $type; 
    }
}
