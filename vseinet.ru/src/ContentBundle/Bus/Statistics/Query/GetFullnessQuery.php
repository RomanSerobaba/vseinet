<?php 

namespace ContentBundle\Bus\Statistics\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GetFullnessQuery extends Message 
{
    /**
     * @Assert\NotBlank(message="Значение categoryId не должно быть пустым")
     * @Assert\Type(type="integer")
     * @VIA\Description("Для получения категорий первого уровня передать 0")
     */
    public $categoryId;

    /**
     * @Assert\NotBlank(message="Значение subject не должно быть пустым")
     * @Assert\Choice({"images", "descriptions", "details", "brands"}, strict=true)
     */
    public $subject;

    /**
     * @Assert\Choice({"alphabetically", "fillness-asc", "fillness-desc"}, strict=true)
     * @VIA\DefaultValue("alphabetically")
     */
    public $sort;

    /**
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(100)
     * @VIA\Description("Процент заполненности")
     */
    public $percentFullness;

    /**
     * @Assert\Choice({"all", "active"}, strict=true)
     * @VIA\DefaultValue("active")
     * @VIA\Description("Статистика по всем или только по активным товарам")
     */
    public $filter;
}