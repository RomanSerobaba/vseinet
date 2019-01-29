<?php

namespace AdminBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GeoPoint
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
     * @Assert\All({
     *     @Assert\Type(type="integer")
     * })
     */
    public $geoRoomIds = [];


    public function __construct($id, $name) 
    {
        $this->id = $id;
        $this->name = $name;
    }
}