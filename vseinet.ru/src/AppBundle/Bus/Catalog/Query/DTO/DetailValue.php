<?php

namespace AppBundle\Bus\Catalog\Query\DTO;

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
     * @Assert\type(type="string")
     */
    public $detailName;

    public function __construct($id, $value, $detailId, $detailName)
    {
        $this->id = $id;
        $this->value = $value;
        $this->detailId = $detailId;
        $this->detailName = $detailName;
    }
}
