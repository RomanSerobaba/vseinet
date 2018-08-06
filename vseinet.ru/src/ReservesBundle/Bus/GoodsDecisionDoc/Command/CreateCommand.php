<?php 

namespace ReservesBundle\Bus\GoodsDecisionDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    

    /**
     * @VIA\Description("Универсальный идентификатор документа-родителя")
     * @Assert\Type(type="integer")
     */
    public $parentDocumentId;

    /**
     * @VIA\Description("Человекочитаемый заголовок")
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @VIA\Description("Статус документа")
     * @Assert\NotBlank(message="Статус документа должен быть указан")
     * @Assert\Choice({"new", "completed"}, strict=true, multiple=false)
     * @VIA\DefaultValue("completed")
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
    
    /**
     * @VIA\Description("Уникальный идентификатор документа - претензии")
     * @Assert\NotBlank(message="Идентификаьтор документа-претензии должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $goodsIssueDocumentId;

    /**
     * @VIA\Description("Идентификатор типа решения")
     * @Assert\NotBlank(message="Идентификатор типа решения должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $goodsDecisionDocTypeId;
    
    //////////////////////////////////////////////
   
    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}