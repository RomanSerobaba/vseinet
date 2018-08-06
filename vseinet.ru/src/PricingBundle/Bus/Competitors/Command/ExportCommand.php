<?php

namespace PricingBundle\Bus\Competitors\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ExportCommand extends Message
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Тип")
     */
    public $type;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Категория")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Менеджер")
     */
    public $managerId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Конкурент")
     */
    public $competitorId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Поиск")
     */
    public $search;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Название файла (не передавать)")
     */
    public $fileName;
}