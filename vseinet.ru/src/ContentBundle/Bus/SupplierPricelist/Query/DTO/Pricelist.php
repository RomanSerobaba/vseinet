<?php 

namespace ContentBundle\Bus\SupplierPricelist\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Pricelist
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
     * @Assert\Type(type="datetime")
     */
    public $uploadedAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $uploadedQuantity;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @Assert\Type(type="datetime")
     */
    public $uploadStartedAt;

    /**
     * @Assert\Type(type="string")
     */
    public $filename;


    public function __construct($id, $name, $uploadedAt, $uploadedQuantity, $isActive, $uploadStartedAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->uploadedAt = $uploadedAt;
        $this->uploadedQuantity = $uploadedQuantity;
        $this->isActive = $isActive;
        $this->uploadStartedAt = $uploadStartedAt;
    }
}