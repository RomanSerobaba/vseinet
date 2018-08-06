<?php 

namespace ReservesBundle\Bus\GoodsDecisionDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{    
    /**
     * @VIA\Description("Уникальный идентификатор изменяемого документа")
     * @Assert\NotBlank(message="Идентификатор изменяемого документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Человекочитаемый заголовок")
     * @Assert\NotBlank(message="Заголовок изменяемого документа должен быть указан")
     * @Assert\Type(type="string")
     */
    public $title;
    
    /**
     * @VIA\Description("Статус документа")
     * @Assert\NotBlank(message="Статус изменяемого документа должен быть указан")
     * @Assert\Choice({"new", "completed"}, strict=true, multiple=false)
     */
    public $statusCode;

    //////////////////////////////////////////////
    
    /**
     * @VIA\Description("Описание решения")
     * @Assert\Type(type="string")
     */
    public $description;
    
    /**
     * @VIA\Description("Количество")
     * @Assert\NotBlank(message="Количество должно быть указанно")
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @VIA\Description("Склад - получатель")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;
    
     /**
     * @VIA\Description("Идентификатор продукта")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;
    
    /**
     * @VIA\Description("Цена продукта")
     * @Assert\Type(type="integer")
     */
    public $price;
    
    /**
     * @VIA\Description("Возвращаемая сумма")
     * @Assert\Type(type="integer")
     */
    public $moneyBack;
    
}