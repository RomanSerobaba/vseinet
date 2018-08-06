<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetSuppliersQuery extends Message 
{
    /**
     * @VIA\Description("Фильтр товаров поставщиков")
     * @Assert\Choice({"all", "active", "hidden"}, strict=true)
     * @VIA\DefaultValue("active")
     */
    public $filter;    
}