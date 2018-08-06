<?php 

namespace ContentBundle\Bus\ParserSource\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @deprecated
 */
class Code
{
    /**
     * @Assert\type(type="string")
     */
    public $type;

    /**
     * @Assert\Choice({"prices", "products", "images", "supplier_products", "tradings"}, strict=true)
     */
    public $code;


    public function __construct($type, $code)
    {
        $this->type = $type;
        $this->code = $code;
    }
}