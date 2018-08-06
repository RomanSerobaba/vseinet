<?php 

namespace ContentBundle\Bus\BaseProduct\Command\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Used in RenameCommand 
 */
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
    public $productId;

    /**
     * @Assert\Type(type="string")
     */
    public $typeCode;

    /**
     * @Assert\Type(type="string")
     */
    public $unitName;

    /**
     * @Assert\Type(type="boolean")
     */
    public $unitSpace;

    /**
     * @Assert\Type(type="integer")
     */
    public $valueId;

    /**
     * @Assert\Type(type="string")
     */
    public $value;

    /**
     * @Assert\Type(type="\stdClass")
     */
    public $substitutions;

    /**
     * @Assert\Type(type="string")
     */
    public $strValue;
    

    public function __construct($id, $name, $productId, $typeCode, $unitName, $unitSpace, $valueId, $value, $substitutions, $strValue)
    {
        $this->id = $id;
        $this->name = $name;
        $this->productId = $productId;
        $this->typeCode = $typeCode;
        $this->unitName = $unitName;
        $this->unitSpace = $unitSpace;
        $this->valueId = $valueId;
        $this->value = $value;
        $this->substitutions = $substitutions ? json_decode($substitutions) : new \stdClass();
        $this->strValue = $strValue;
    }

    public function getFormedValue()
    {
        switch ($this->typeCode) {
            case 'enum':
            case 'string':
                return $this->strValue;

            case 'boolean':
                if (null === $this->value) {
                    return null;
                }

                return $this->substitutions->nameY ?: $this->name;

            case 'number':
                if (null === $this->value) {
                    return null;
                }
                $value = $this->value;
                if ($this->unitName) {
                    if ($this->unitSpace) {
                        $value .= ' ';
                    }
                    $value .= $this->unitName;
                }

                return $value;

            case 'dimensions':
            case 'size':
            case 'range':
                $values = [];
                foreach ($this->depends as $depend) {
                    if (null !== $depend->value) {
                        $values[] = $depend->value; 
                    }
                }
                if (empty($values)) {
                    return null;
                }
                $glue = 'range' == $this->typeCode ? '..' : 'x';

                return implode($glue, $value);
        }

        return null;
    }
}