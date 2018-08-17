<?php 

namespace AppBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\Constraints as VIC;
use AppBundle\Enum\DetailType;

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
     * @Assert\Type(type="string")
     */
    public $group;

    /**
     * @VIC\Enum("AppBundle\Enum\DetailType")
     */
    public $typeCode;

    /**
     * @Assert\Type(type="string")
     */
    public $unit;

    /**
     * @Assert\Type(type="string")
     */
    public $value;


    public function __construct($id, $name, $group, $typeCode, $unit, $value, $number, $memo)
    {
        $this->id = $id;
        $this->name = $name;
        $this->group = $group;
        $this->typeCode = $typeCode;
        $this->unit = $unit;

        switch ($typeCode) {
            case DetailType::CODE_ENUM:
            case DetailType::CODE_STRING:
                $this->value = $value;
                break;

            case DetailType::CODE_ENUM:
                $this->value = $memo;
                break;

            default:
                $this->value = $number;
        }
    }
}
