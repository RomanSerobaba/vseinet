<?php 

namespace ContentBundle\Bus\SupplierPricelist\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Supplier
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $code;


    public function __construct($id, $code)
    {
        $this->id = $id;
        $this->code = $code;
    }
}