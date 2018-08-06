<?php 

namespace ShopBundle\Bus\Banner\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommand extends Message
{
    /**
     * @VIA\Description("Баннер название")
     * @Assert\NotBlank(message="Название баннера не указано")
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @VIA\Description("Баннер тип")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $type;

    /**
     * @VIA\Description("Баннер вес")
     * @Assert\NotBlank
     * @Assert\Type(type="integer")
     */
    public $weight;

    /**
     * @VIA\Description("URL")
     * @Assert\Type(type="string")
     */
    public $url;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Изображение таба")
     */
    public $tabImg;

    /**
     * @VIA\Description("Цвет фона таба")
     * @Assert\Type(type="string")
     */
    public $tabBackgroundColor;

    /**
     * @VIA\Description("Цвет текста таба")
     * @Assert\Type(type="string")
     */
    public $tabTextColor;

    /**
     * @VIA\Description("Закрепить таб")
     * @Assert\Type(type="boolean")
     */
    public $tabIsFixed;

    /**
     * @VIA\Description("Цвет фона")
     * @Assert\Type(type="string")
     */
    public $backgroundColor;

    /**
     * @VIA\Description("Цвет текста")
     * @Assert\Type(type="string")
     */
    public $titleTextColor;

    /**
     * @VIA\Description("Шаблон")
     * @Assert\Type(type="integer")
     */
    public $templatesId;

    /**
     * @VIA\Description("Активен")
     * @Assert\Type(type="boolean")
     */
    public $isVisible;

    /**
     * @VIA\Description("Дата показа от")
     * @Assert\Type(type="datetime")
     */
    public $startVisibleDate;

    /**
     * @VIA\Description("Дата показа до")
     * @Assert\Type(type="datetime")
     */
    public $endVisibleDate;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPcX;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPcY;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundTabletX;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundTabletY;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPhoneX;

    /**
     * @Assert\Type(type="integer")
     */
    public $posBackgroundPhoneY;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}