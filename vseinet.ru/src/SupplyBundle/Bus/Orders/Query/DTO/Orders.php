<?php

namespace SupplyBundle\Bus\Orders\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Orders
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $orderId;

    /**
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Type(type="datetime")
     */
    public $orderItemStatusUpdatedAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $retailPrice;

    /**
     * @Assert\Type(type="integer")
     */
    public $transportCharges;

    /**
     * @Assert\Type(type="integer")
     */
    public $franchiserClientPrice;

    /**
     * @Assert\Type(type="integer")
     */
    public $prepayment;

    /**
     * @Assert\Type(type="integer")
     */
    public $requiredPrepayment;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isAnnulled;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isCompleted;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isRefunded;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isClarificationNeeded;

    /**
     * @Assert\Type(type="integer")
     */
    public $initialRetailPrice;

    /**
     * @Assert\Type(type="integer")
     */
    public $discountAmount;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierId;

    /**
     * @Assert\Type(type="string")
     */
    public $supplierReserve;

    /**
     * @Assert\Type(type="string")
     */
    public $initialProductAvailabilityCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplyId;

    /**
     * @Assert\Type(type="integer")
     */
    public $retailPriceUpdatedBy;

    /**
     * @Assert\Type(type="datetime")
     */
    public $retailPriceUpdatedAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $createdBy;

    /**
     * @Assert\Type(type="string")
     */
    public $orderItemStatusCode;

    /**
     * @Assert\Type(type="string")
     */
    public $fullname;

    /**
     * @Assert\Type(type="string")
     */
    public $phone;

    /**
     * @Assert\Type(type="string")
     */
    public $email;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isNotReached;

    /**
     * @Assert\Type(type="string")
     */
    public $statusName;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="string")
     */
    public $city;

    /**
     * @Assert\Type(type="boolean")
     */
    public $orderHasComments;

    /**
     * @Assert\Type(type="boolean")
     */
    public $orderItemHasComments;

    /**
     * @Assert\Type(type="string")
     */
    public $paymentTypeCode;

    /**
     * @Assert\Type(type="string")
     */
    public $paymentTypeName;

    /**
     * @Assert\Type(type="string")
     */
    public $ourSellerCounteragentName;

    /**
     * @Assert\Type(type="integer")
     */
    public $ourSellerCounteragentId;

    /**
     * @Assert\Type(type="string")
     */
    public $orderManager;

    /**
     * @Assert\Type(type="string")
     */
    public $orderItemCreator;

    /**
     * @Assert\Type(type="boolean")
     */
    public $canBeAnnulled;

    /**
     * @Assert\Type(type="string")
     */
    public $supplierIds;

    /**
     * @Assert\Type(type="array")
     */
    public $routes;
}