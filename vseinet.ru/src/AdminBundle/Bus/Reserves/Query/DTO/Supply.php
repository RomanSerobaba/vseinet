<?php

namespace AdminBundle\Bus\Reserves\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class Supply
{
    /**
     * @Assert\Type(type="integer")
     */
    public $supplyId;

    /**
     * @Assert\Type(type="string")
     */
    public $supplyNumber;

    /**
     * @Assert\Type(type="string")
     */
    public $supplierCode;

    /**
     * @Assert\Type(type="datetime")
     */
    public $supplyCreatedAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $purchasePrice;

    /**
     * @Assert\Type(type="integer")
     */
    public $freeDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $freeReservedDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $freeTransitDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $reservedDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $reservedTransitDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $issuedDelta = 0;

    /**
     * @Assert\Type(type="integer")
     */
    public $issuedTransitDelta = 0;


    public function __construct(
        $supplyId,
        $supplyNumber,
        $supplierCode,
        $supplyCreatedAt,
        $purchasePrice
    )
    {
        $this->supplyId = $supplyId;
        $this->supplyNumber = $supplyNumber;
        $this->supplierCode = $supplierCode;
        $this->supplyCreatedAt = $supplyCreatedAt;
        $this->purchasePrice = $purchasePrice;
    }
}
