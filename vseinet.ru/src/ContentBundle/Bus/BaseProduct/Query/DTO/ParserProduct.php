<?php 

namespace ContentBundle\Bus\BaseProduct\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class ParserProduct
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $source;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     * @Assert\Url
     */
    public $url;

    /**
     * @Assert\Type(type="string")
     */
    public $brand;

    /**
     * @Assert\Type(type="string")
     */
    public $description;


    public function __construct($id, $source, $name, $url, $brand, $description)
    {
        $this->id = $id;
        $this->source = $source;
        $this->name = $name;
        $this->url = $url;
        $this->brand = $brand;
        $this->description = $description;
    }
}