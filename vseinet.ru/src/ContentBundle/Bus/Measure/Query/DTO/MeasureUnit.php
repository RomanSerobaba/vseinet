<?php

namespace ContentBundle\Bus\Measure\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class MeasureUnit
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $measureId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="float")
     */
    public $k;

    /**
     * @Assert\Type(type="boolean")
     */
    public $useSpace;

    /**
     * @Assert\Type(type="array<string>")
     */
    public $aliases = [];

    /**
     * @Assert\Type(type="boolean")
     */
    public $isUsed;
    

    public function __construct($id, $measureId, $name, $k, $useSpace, $aliases, $isUsed)
    {
        $this->id = $id;
        $this->measureId = $measureId;
        $this->name = $name;
        $this->k = $k;
        $this->useSpace = $useSpace;
        if ($aliases) {
            $this->aliases = explode(',', $aliases);
        }
        $this->isUsed = $isUsed;
    }
}

