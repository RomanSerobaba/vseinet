<?php 

namespace ContentBundle\Bus\CategorySection\Query\DTO;

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
     * @Assert\Type(type="array<integer>")
     */
    public $detailIds = [];


    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}