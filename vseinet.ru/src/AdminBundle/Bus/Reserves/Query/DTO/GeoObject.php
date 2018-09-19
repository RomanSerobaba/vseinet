<?php 

namespace AdminBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GeoObject
{
    /**
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;
    
    /**
     * @Assert\Type(type="string")
     */
    public $geoRoomName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoPointId;
    
    /**
     * @Assert\Type(type="string")
     */
    public $geoPointName;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoCityId;
    
    /**
     * @Assert\Type(type="string")
     */
    public $geoCityName;
}
