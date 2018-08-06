<?php 

namespace SiteBundle\Bus\Catalog\Query\DTO\Filter;

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
    public $name;


    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
