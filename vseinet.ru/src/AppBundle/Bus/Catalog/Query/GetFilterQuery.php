<?php 

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetFilterQuery extends Message 
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(0)
     * @VIA\Description("Id категории")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование бренда в адресной строке")
     */
    public $brand;

    /**
     * @Assert\Choice({"best", "rating", "price", "novelty", "name"}, strict=true)
     * @VIA\DefaultValue("default")
     * @VIA\Description("Сортировка")
     */
    public $sort = 'default';

    /**
     * @Assert\Choice({"asc", "desc"}, strict=true)
     * @VIA\DefaultValue("asc")
     * @VIA\Description("Направление сортировки")
     */
    public $sortdirect = 'asc';

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Id-ы брендов разделенные |")
     */
    public $brands;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Диапазон цен разделенные |")
     */
    public $price;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Характеристики в формате [Id_характеристики>]=Id_значения или диапазон значений разделенные |")
     */
    public $details;

    /**
     * @Assert\Type(type="string")
     * @Assert\Length(min=1)
     * @VIA\Description("Поисковый запрос")
     */
    public $query;

    /**
     * @Assert\Choice({"active", "onorder", "onstock", "all"}, strict=true)
     * @VIA\Description("active")
     * @VIA\Description("Доступность")
     */
    public $available;

    /**
     * @Assert\Choice({"all", "gt2mon", "gt3mon", "gt6mon", "gt9mon", "gt12mon"}, strict="true")
     * @VIA\DefaultValue("all")
     * @VIA\Description("Залежавшийся товар")
     */
    public $unsold = 'all';

    /**
     * @Assert\All({
     *     @Assert\Choice({"images", "details", "description", "manual-link", "manufacturer-link"}, strict=true)
     * })
     * @VIA\Description("Заполненность")
     */
    public $nofilled;

    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(1)
     */
    public $page = 1;
}