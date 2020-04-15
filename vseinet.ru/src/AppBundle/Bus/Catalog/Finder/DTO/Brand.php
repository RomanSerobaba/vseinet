<?php

namespace AppBundle\Bus\Catalog\Finder\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Brand
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $aliasId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $sefName;

    /**
     * @Assert\Type(type="integer")
     */
    public $countProducts = 0;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isTop;

    /**
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $aliasIds = [];

    public function __construct($id, $name, $sefName= null, $aliasId = null)
    {
        $this->id = $id;
        $this->aliasId = $aliasId;
        $this->name = $name;
        $this->sefName = $sefName;
    }
}
