<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetBaseProductsQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение categoryId не должно быть пустым")
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @VIA\Description("Фильтр товаров")
     * @Assert\Choice({"all", "active", "new", "old", "hidden"}, strict=true)
     * @VIA\DefaultValue("all")
     */
    public $filter;
}