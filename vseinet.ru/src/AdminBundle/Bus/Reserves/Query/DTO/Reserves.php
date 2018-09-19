<?php 

namespace AdminBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Reserves
{
    /**
     * @Assert\All({
     *     @Assert\Type(type="AdminBundle\Bus\Reserves\Query\DTO\GeoCity")
     * })
     */
    public $geoCities;

    /*
     * @Assert\All({
     *     @Assert\Type(type="AdminBundle\Bus\Reserves\Query\DTO\GeoPoint")
     * })
     */
    public $geoPoints;

    /*
     * @Assert\All({
     *     @Assert\Type(type="AdminBundle\Bus\Reserves\Query\DTO\GeoRoom")
     * })
     */
    public $geoRooms;
    
    /*
     * @Assert\All({
     *     @Assert\Type(type="AdminBundle\Bus\Reserves\Query\DTO\Supply")
     * })
     */
    public $supplies;


    public function __construct(array $geoCities = [], array $geoPoints = [], array $geoRooms = [], array $supplies = [])
    {
        $this->geoCities = $geoCities;
        $this->geoPoints = $geoPoints;
        $this->geoRooms = $geoRooms;
        $this->supplies = $supplies;
    }
}
