<?php 

namespace GeoBundle\Service\DTO;

class Representative implements \Serializable
{
    /**
     * @var int
     */
    public $geoCityId;

    /**
     * @var int
     */
    public $geoPointId;


    public function __construct($geoCityId, $geoPointId)
    {
        $this->geoCityId = $geoCityId;
        $this->geoPointId = $geoPointId;
    }

    public function serialize()
    {
        return json_encode([$this->geoCityId,$this->geoPointId]);
    }

    public function unserialize($serialized)
    {
        list($this->geoCityId, $this->geoPointId) = json_decode($serialized, true);  
    }
}
