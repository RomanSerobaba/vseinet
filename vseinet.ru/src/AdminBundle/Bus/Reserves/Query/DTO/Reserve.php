<?php

namespace AdminBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class Reserve
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
    public $orderItemId;

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

    /**
     * @Assert\Type(type="integer")
     */
    public $goodsPalletId;


    public function __construct(
        $did,
        $geoRoomId,
        $orderItemId,
        $destinationGeoRoomId,
        $code,
        $number,
        $createdAt,
        $delta,
        $purchasePrice,
        $goodsConditionCode,
        $goodsPalletId
    )
    {
        $this->did = $did;
        $this->roomId = $geoRoomId;
        $this->orderItemId = $orderItemId;
        $this->destinationGeoRoomId = $destinationGeoRoomId;
        $this->code = $code;
        $this->number = $number;
        $this->createdAt = $createdAt;
        $this->delta = $delta;
        $this->purchasePrice = $purchasePrice;
        $this->goodsConditionCode = $goodsConditionCode;
        $this->goodsPalletId = $goodsPalletId;
    }
}
