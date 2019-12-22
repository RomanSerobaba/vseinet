<?php

namespace AppBundle\Bus\Catalog\Query\DTO;

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

    /**
     * @Assert\Type(type="string")
     */
    public $sefUrl;


    public function __construct($id, $name, $aliasForId, $countProducts, $sefUrl = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->aliasForId = $aliasForId;
        $this->countProducts = $countProducts;
        $this->sefUrl = $sefUrl;
    }
}
