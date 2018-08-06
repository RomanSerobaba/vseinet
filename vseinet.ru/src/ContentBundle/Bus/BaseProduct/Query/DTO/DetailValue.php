<?php 

namespace ContentBundle\Bus\BaseProduct\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DetailValue
{
    /**
     * @Assert\Type(type="integer")
     */
    public $detailId;

    /**
     * @Assert\Type(type="string")
     */
    public $value;


    public function __construct($detailId, $typeCode, $value, $valueId = null)
    {
        $this->detailId = $detailId;
        switch ($typeCode) {
            case 'string':
            case 'enum':
                $this->value = $valueId;
                break;

            case 'boolean':
                $this->value = $value ? true : false;
                break;

            default:
                $this->value = $value;
        }
    }
}