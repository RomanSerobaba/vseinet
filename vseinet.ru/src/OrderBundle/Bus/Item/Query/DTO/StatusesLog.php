<?php 

namespace OrderBundle\Bus\Item\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class StatusesLog
{
    /**
     * @Assert\Type(type="datetime")
     */
    public $updatedAt;

    /**
     * @Assert\Type(type="integer")
     */
    public $updatedBy;

    /**
     * @Assert\Type(type="string")
     */
    public $name;
    
    /**
     * OrderItemStatusesLog constructor.
     *
     * @param $updatedBy
     * @param $updatedAt
     * @param $name
     */
    public function __construct($name, $updatedAt, $updatedBy)
    {
        $this->updatedAt = $updatedAt;
        $this->updatedBy = $updatedBy;
        $this->name = $name;
    }
}