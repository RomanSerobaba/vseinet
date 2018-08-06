<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

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

    /**
     * @Assert\Type(type="integer")
     */
    public $countProducts;


    public function __construct($id, $code,  $countProducts)
    {
        $this->id = $id;
        $this->code = $code;
        $this->countProducts = intval($countProducts);
    }
}