<?php 

namespace ContentBundle\Bus\DetailValueAudit\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DetailValue
{    
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $value;

    /**
     * @Assert\Type(type="integer")
     */
    public $detailId;

    /**
     * @Assert\Type(type="string")
     */
    public $createdBy;

    /**
     * @Assert\Type(type="datetime")
     */
    public $createdAt;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isVerified;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isUsed;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $aliasIds = [];


    public function __construct($id, $value, $detailId, $createdBy, $createdAt, $isVerified, $isUsed)
    {
        $this->id = $id;
        $this->value = $value;
        $this->detailId = $detailId;
        $this->createdBy = $createdBy ?: null;
        $this->createdAt = $createdAt;
        $this->isVerified = $isVerified;
        $this->isUsed = $isUsed;
    }
}