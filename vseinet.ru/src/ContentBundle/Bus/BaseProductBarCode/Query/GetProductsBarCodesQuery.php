<?php 

namespace ContentBundle\Bus\BaseProductBarCode\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetProductsBarCodesQuery extends Message 
{
    /**
     * @VIA\Description("Штрихкод")
     * @Assert\Type(type="string")
     */
    public $barCode;
    
    /**
     * @VIA\Description("Номер страницы")
     * @Assert\Type(type="integer")
     */
    public $page;
    
    /**
     * @VIA\Description("Длинна страницы")
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(35)
     */
    public $limit;
    
}