<?php 

namespace ReservesBundle\Bus\GoodsPackaging\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    

    /**
     * @VIA\Description("Заголовок")
     * @Assert\Type(type="string")
     */
    public $title;
    
    /**
     * @VIA\Description("Строка типа документа: 'combining' или 'fractionation'")
     * @Assert\NotBlank(message="Тип комплектации/разкомплектации должен быть указан")
     * @Assert\Choice({"combining", "fractionation"}, strict=true)
     */
    public $type;
    
    /**
     * @VIA\Description("Место хранения")
     * @Assert\NotBlank(message="Место хранения товаров должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\NotBlank(message="Идентификатор товара должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Количество товара")
     * @Assert\NotBlank(message="Количество товара должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}
