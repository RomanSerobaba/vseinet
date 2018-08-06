<?php 

namespace SiteBundle\Bus\Catalog\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Subcategory
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
    public $aliasForId;

    /**
     * @Assert\Type(type="integer")
     */
    public $countProducts;

    /**
     * @Assert\Type(type="string")
     */
    public $baseSrc;


    public function __construct($id, $name, $aliasForId, $countProducts) 
    {
        $this->id = $id;
        $this->name = $name;
        $this->aliasForId = $aliasForId;
        $this->countProducts = $countProducts;
    }
}
