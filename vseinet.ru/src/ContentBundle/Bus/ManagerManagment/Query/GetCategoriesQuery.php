<?php 

namespace ContentBundle\Bus\ManagerManagment\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetCategoriesQuery extends Message
{
    /**
     * @Assert\NotBlank(message="Значение pid не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Для получения категорий первого уровня передать 0")
     */
    public $pid;

    /**
     * @Assert\Choice({
     *     "all",
     *     "with-tpl",
     *     "without-contenter"
     * }, strict=true)
     * @VIA\DefaultValue("all")
     * @VIA\Description("
     *     all => все,
     *     with-tpl => есть шаблон,
     *     without-contenter => без контент-менеджера
     * ")
     */
    public $filter;
}