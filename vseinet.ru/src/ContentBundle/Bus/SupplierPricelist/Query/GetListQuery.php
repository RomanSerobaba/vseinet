<?php 

namespace ContentBundle\Bus\SupplierPricelist\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetListQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение supplierId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $supplierId;

    /**
     * @Assert\Choice({"all", "active"}, strict=true)
     * @VIA\DefaultValue("all")
     */
    public $filter;
}