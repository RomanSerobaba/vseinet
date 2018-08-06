<?php 

namespace SupplyBundle\Bus\Shipment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SupplierSupplies
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $bonusAmount;

    /**
     * @Assert\Type(type="string")
     */
    public $status;

    /**
     * @Assert\Type(type="integer")
     */
    public $ourCounteragentId;

    /**
     * @Assert\Type(type="integer")
     */
    public $ourCounteragentName;

    /**
     * SupplierSupplies constructor.
     *
     * @param $id
     * @param $createdAt
     * @param $bonusAmount
     * @param $status
     * @param $ourCounteragentId
     * @param $ourCounteragentName
     */
    public function __construct($id, $createdAt, $bonusAmount, $status, $ourCounteragentId, $ourCounteragentName)
    {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->bonusAmount = $bonusAmount;
        $this->status = $status;
        $this->ourCounteragentId = $ourCounteragentId;
        $this->ourCounteragentName = $ourCounteragentName;
    }
}