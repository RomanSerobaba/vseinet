<?php 

namespace PricingBundle\Bus\Competitors\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetRevisionQuery extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Конкурент")
     * @Assert\NotBlank(message="Конкурент должен быть указан")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Город")
     */
    public $cityId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Режим просмотра")
     * @Assert\Choice({"loosing", "actual", "outdated"}, strict=true)
     * @VIA\DefaultValue("loosing")
     */
    public $viewMode;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Категория")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Канал")
     */
    public $channel;

    /**
     * @Assert\Type(type="datetime")
     * @VIA\Description("Дата создания с")
     */
    public $createdFrom;

    /**
     * @Assert\Type(type="datetime")
     * @VIA\Description("Дата создания по")
     */
    public $createdTill;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Создатель")
     */
    public $createdBy;

    /**
     * @VIA\Description("Номер страницы")
     * @Assert\Type(type="integer")
     */
    public $page;

    /**
     * @VIA\Description("Длинна страницы")
     * @Assert\Type(type="integer")
     */
    public $limit;
}