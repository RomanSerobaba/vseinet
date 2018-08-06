<?php 

namespace ContentBundle\Bus\DetailValueAudit\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Detail
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
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="string")
     */
    public $typeCode;


    public function __construct($id, $name, $categoryId, $typeCode)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->typeCode = $typeCode;
    }
}