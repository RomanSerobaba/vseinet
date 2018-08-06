<?php 

namespace ContentBundle\Bus\BaseProductBarCode\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetProductsQuery extends Message 
{
    /**
     * @VIA\Description("Штрихкод")
     * @Assert\Type(type="string")
     */
    public $barCode;
    
    /**
     * @VIA\Description("Не искать среди товара")
     * @Assert\Type(type="boolean")
     */
    public $withOutProducts;
    
    /**
     * @VIA\Description("Не искать среди паллет")
     * @Assert\Type(type="boolean")
     */
    public $withOutPallets;

    
}