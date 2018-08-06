<?php 

namespace ContentBundle\Bus\SupplierPricelist\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetSuppliersQuery extends Message
{
    /**
     * @Assert\Choice({"all", "active"}, strict=true)
     * @VIA\DefaultValue("all")
     */
    public $filter;
}