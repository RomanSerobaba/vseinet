<?php 

namespace ContentBundle\Bus\BaseProduct\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SupplierProduct
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $supplier;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $url;


    public function __construct($id, $supplier, $name, $url)
    {
        $this->id = $id;
        $this->supplier = $supplier;
        $this->name = $name;
        $this->url = $url;
    }
}