<?php 

namespace ShopBundle\Bus\Banner\Command;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class AddProductCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор баннера")
     * @Assert\NotBlank(message="Идентификатор баннера не указан")
     * @Assert\Type(type="integer")
     */
    public $bannerId;
    
    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\NotBlank(message="Идентификатор товара не указан")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;
    
    /**
     * @VIA\Description("Название товара")
     * @Assert\Type(type="string")
     */
    public $title;
    
    /**
     * @VIA\Description("Фото товара для PC")
     * @Assert\Type(type="string")
     */
    public $photoPc;
    
    /**
     * @VIA\Description("Фото товара для планшета")
     * @Assert\Type(type="string")
     */
    public $photoTablet;
    
    /**
     * @VIA\Description("Фото товара для телефона")
     * @Assert\Type(type="string")
     */
    public $photoPhone;
    
    /**
     * @VIA\Description("Название товара для PC")
     * @Assert\Type(type="string")
     */
    public $titlePc;
    
    /**
     * @VIA\Description("Название товара для планшета")
     * @Assert\Type(type="string")
     */
    public $titleTablet;
    
    /**
     * @VIA\Description("Название товара для телефона")
     * @Assert\Type(type="string")
     */
    public $titlePhone;
    
    /**
     * @VIA\Description("Старая цена товара")
     * @Assert\Type(type="integer")
     */
    public $price;
    
    /**
     * @VIA\Description("Цена товара со скидкой")
     * @Assert\Type(type="integer")
     */
    public $salePrice;
    
    /**
     * @Assert\Uuid
     */
    public $uuid;
}