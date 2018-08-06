<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

/**
 * @deprecated
 */
class GetChildrenQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение id не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Для получения первого уровня передать 0")
     */
    public $id;

    /**
     * @Assert\Choice({"all", "with-tpl", "without-tpl"}, strict=true)
     * @VIA\DefaultValue("all")
     * @VIA\Description("Фильтр отбора категорий")
     */
    public $filter;
}