<?php

namespace AppBundle\Bus\Catalog\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Breadcrumb
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
     * @Assert\Type(type="string")
     */
    public $sefUrl;


    public function __construct($id, $name, $sefUrl)
    {
        $this->id = $id;
        $this->name = $name;
        $this->sefUrl = $sefUrl;
    }
}
