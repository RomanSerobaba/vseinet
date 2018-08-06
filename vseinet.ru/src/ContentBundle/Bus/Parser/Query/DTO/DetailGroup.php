<?php 

namespace ContentBundle\Bus\Parser\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DetailGroup
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
    public $sourceId;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $detailIds = [];


    public function __construct($id, $name, $sourceId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sourceId = $sourceId;
    }
}