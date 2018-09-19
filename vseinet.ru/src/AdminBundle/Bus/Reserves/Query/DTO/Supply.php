<?php 

namespace AdminBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class Supply
{
    /**
     * @Assert\Type(type="integer")
     */
    public $did;

    /**
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @Assert\Type(type="integer")
     */
    public $destinationGeoRoomId;

    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $number;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $delta;

    /**
     * @Assert\Type(type="integer")
     */
    public $purchasePrice;

    /**
     * @Enum("AppBundle\Enum\GoodsConditionCode")
     */
    public $goodsConditionCode;


    public function __construct(
        $did, 
        $geoRoomId,
        $destinationGeoRoomId,
        $code, 
        $number, 
        $createdAt, 
        $delta, 
        $purchasePrice, 
        $goodsConditionCode
    )
    {
        $this->did = $did;
        $this->roomId = $roomId;
        $this->destinationGeoRoomId = $destinationGeoRoomId;
        $this->code = $code;
        $this->number = $number;
        $this->createdAt = $createdAt;
        $this->delta = $delta;
        $this->purchasePrice = $purchasePrice;
        $this->goodsConditionCode = $goodsConditionCode; 
    }
}
