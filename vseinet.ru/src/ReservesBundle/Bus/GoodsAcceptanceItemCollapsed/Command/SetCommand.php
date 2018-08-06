<?php 

namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class SetCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор документа")
     * @Assert\NotBlank(message="Идентификатор изменяемого документа должен быть заполнен")
     * @Assert\GreaterThan(0)
     * @Assert\Type(type="integer")
     */
    public $goodsAcceptanceId;

    /**
     * @VIA\Description("Идентификатор продукта")
     * @Assert\NotBlank(message="Идентификатор продукта должен быть заполнен")
     * @Assert\GreaterThan(0)
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Тип элемента списка товар/палета (product/pallete)")
     * @Assert\Type(type="string")
     * @VIA\DefaultValue("product")
     */
    public $type;
    
    /**
     * @VIA\Description("Код состояния товара")
     * @Assert\Type(type="string")
     * @VIA\DefaultValue("normal")
     */
    public $goodsStateCode;

    /**
     * @VIA\Description("Идентификатор направления")
     * @Assert\NotBlank(message="Идентификатор направления должен быть заполнен")
     * @Assert\GreaterThan(0)
     * @Assert\Type(type="integer")
     */
    public $geoPointId;
    
    /**
     * @VIA\Description("Количество обрабатываемого товара")
     * @Assert\NotBlank(message="Количество обрабатываемого товара должно быть заполнено")
     * @Assert\GreaterThanOrEqual(0)
     * @Assert\Type(type="integer")
     */
    public $quantity;
    
    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}