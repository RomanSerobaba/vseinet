<?php

namespace AdminBundle\Bus\Supplier\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints\Enum;

class Remain
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $supplierCode;

    /**
     * @Assert\Type(type="string")
     */
    public $article;

    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Enum("AppBundle\Enum\ProductAvailabilityCode")
     */
    public $productAvailabilityCode;

    /**
     * @Assert\Type(type="integer")
     */
    public $price;

    /**
     * @Assert\Type(type="datetime")
     */
    public $priceTime;

    /**
     * @Assert\Type(type="string")
     */
    public $transferedBy;

    /**
     * @Assert\Type(type="datetime")
     */
    public $transferedAt;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isSupplier;
}
