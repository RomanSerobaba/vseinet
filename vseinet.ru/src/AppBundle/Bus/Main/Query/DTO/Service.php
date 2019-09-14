<?php

namespace AppBundle\Bus\Main\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Service
{
    /**
     * @Assert\Type(type="string")
     */
    public $url;

    /**
     * @Assert\Type(type="string")
     */
    public $slug;

    /**
     * @Assert\Type(type="string")
     */
    public $titleShort;

    /**
     * @Assert\Type(type="integer")
     */
    public $id;


    public function __construct($id, $url,  $slug, $titleShort)
    {
        $this->id = $id;
        $this->url = $url;
        $this->slug = $slug;
        $this->titleShort = $titleShort;
    }
}
