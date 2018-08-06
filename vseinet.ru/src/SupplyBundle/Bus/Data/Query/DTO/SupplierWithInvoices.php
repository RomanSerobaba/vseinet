<?php 

namespace SupplyBundle\Bus\Data\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class SupplierWithInvoices
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="datetime")
     */
    public $date;

    /**
     * @Assert\Type(type="string")
     */
    public $point;

    /**
     * @Assert\Type(type="integer")
     */
    public $sum;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="datetime")
     */
    public $arrivingTime;

    /**
     * @Assert\Type(type="integer")
     */
    public $waybillNumber;

    /**
     * @Assert\Type(type="datetime")
     */
    public $waybillDate;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierCounteragentId;

    /**
     * @Assert\Type(type="string")
     */
    public $ourCounteragent;

    /**
     * @Assert\Type(type="string")
     */
    public $supplierInvoiceNumber;

    /**
     * @Assert\Type(type="string")
     */
    public $comment;

    /**
     * @Assert\Type(type="string")
     */
    public $creator;

    /**
     * @Assert\Type(type="string")
     */
    public $state;
}