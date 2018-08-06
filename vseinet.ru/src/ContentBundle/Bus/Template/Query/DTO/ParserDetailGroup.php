<?php 

namespace ContentBundle\Bus\Template\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ParserDetailGroup
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


    public function __construct($id, $name, $sourceId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sourceId = $sourceId;
    }
}