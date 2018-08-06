<?php 

namespace ContentBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DetailValue
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $detailId;

    /**
     * @Assert\Type(type="string")
     */
    public $value;


    public function __construct($id, $detailId, $value)
    {
        $this->id = $id;
        $this->detailId = $detailId;
        $this->value = $value;
    }
}