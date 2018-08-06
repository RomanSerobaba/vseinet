<?php 

namespace ShopBundle\Bus\Banner\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class EditCommand extends Message
{
    /**
     * @VIA\Description("Баннер id")
     * @Assert\NotBlank(message="Идентификатор баннера не указан")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Тип баннера")
     * @Assert\NotBlank(message="Тип баннера не указан")
     * @Assert\Type(type="integer")
     */
    public $type;

    /**
     * @VIA\Description("Позиция баннера")
     * @Assert\NotBlank(message="Позиция баннера не указана")
     * @Assert\Type(type="integer")
     */
    public $weight;

    /**
     * @VIA\Description("Баннер название")
     * @Assert\NotBlank(message="Название баннера не указано")
     * @Assert\Type(type="string")
     */
    public $title;

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
     * @Assert\Type(type="string")
     * @VIA\Description("Изображение фона для PC")
     */
    public $imgBackgroundPc;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Изображение фона для планшета")
     */
    public $imgBackgroundTablet;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Изображение фона для телефона")
     */
    public $imgBackgroundPhone;

    /**
     * @Assert\Type(type="string")
     */
    public $descriptionPc;

    /**
     * @Assert\Type(type="string")
     */
    public $descriptionTablet;

    /**
     * @Assert\Type(type="string")
     */
    public $descriptionPhone;

    /**
     * @Assert\Type(type="string")
     */
    public $titlePc;

    /**
     * @Assert\Type(type="string")
     */
    public $titleTablet;

    /**
     * @Assert\Type(type="string")
     */
    public $titlePhone;


    /**
     * @Assert\Type(type="string")
     */
    public $textUrlPc;

    /**
     * @Assert\Type(type="string")
     */
    public $textUrlTablet;

    /**
     * @Assert\Type(type="string")
     */
    public $textUrlPhone;

    /**
     * @VIA\Description("Цвет фона таба")
     * @Assert\Type(type="string")
     */
    public $tabBackgroundColor;

    /**
     * @VIA\Description("Текст таба")
     * @Assert\Type(type="string")
     */
    public $tabText;

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
     * @Assert\NotBlank(message="Не указан шаблон")
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
     * @VIA\Description("Первая колонка характеристик на PC")
     * @Assert\Type(type="string")
     */
    public $leftDetailsPc;
    
    /**
     * @VIA\Description("Первая колонка характеристик на планшете")
     * @Assert\Type(type="string")
     */
    public $leftDetailsTablet;
    
    /**
     * @VIA\Description("Первая колонка характеристик на смартфоне")
     * @Assert\Type(type="string")
     */
    public $leftDetailsPhone;
    
    /**
     * @VIA\Description("Вторая колонка характеристик на PC")
     * @Assert\Type(type="string")
     */
    public $rightDetailsPc;
    
    /**
     * @VIA\Description("Вторая колонка характеристик на планшете")
     * @Assert\Type(type="string")
     */
    public $rightDetailsTablet;
    
    /**
     * @VIA\Description("Вторая колонка характеристик на смартфоне")
     * @Assert\Type(type="string")
     */
    public $rightDetailsPhone;

    /**
     * @Assert\Uuid
     */
    public $uuid;
}