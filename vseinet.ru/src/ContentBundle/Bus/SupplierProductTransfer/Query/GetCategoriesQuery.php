<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetCategoriesQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение pid не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Для получения первого уровня передать 0")
     */
    public $pid;

    /**
     * @VIA\Description("Фильтр товаров")
     * @Assert\Choice({"all", "active", "new", "old", "hidden"}, strict=true)
     * @VIA\DefaultValue("all")
     */
    public $filter;
}