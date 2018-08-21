<?php 

namespace AppBundle\Bus\Geo\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;
use AppBundle\Enum\RepresentativeTypeCode;

class Contact
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
     * @Assert\Type(type="AppBundle\Doctrine\DBAL\ValueObject\Point")
     */
    public $coordinates;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isOur;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isNew;

    /**
     * @Assert\Type(type="array<string>")
     */
    public $phones;

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
        $deliveryTax,
        $coordinates,
        $openingDate
    ) {
        $this->geoPointId = $geoPointId;
        $this->geoRegionId = $geoRegionId;
        $this->geoRegionName = $geoRegionName;
        $this->geoCityId = $geoCityId;
        $this->geoCityName = $geoCityName;
        $this->address = $address;
        $this->hasRetail = $hasRetail;
        $this->hasDelivery = $hasDelivery;
        $this->deliveryTax = $deliveryTax;
        $this->coordinates =$coordinates;

        $this->isNew = $openingDate && $openingDate > new \DateTime('-2 months');
        
        $this->isOur = RepresentativeTypeCode::OUR === $type; 
        $this->type = $hasRetail ? RepresentativeTypeCode::RETAIL : RepresentativeTypeCode::COURIER;
    }
}
