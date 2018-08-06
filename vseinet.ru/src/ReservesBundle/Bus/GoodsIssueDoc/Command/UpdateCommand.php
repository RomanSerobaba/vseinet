<?php 

namespace ReservesBundle\Bus\GoodsIssueDoc\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{    
    /**
     * @VIA\Description("Уникальный идентификатор документа")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Человекочитаемый заголовок")
     * @Assert\Type(type="string")
     */
    public $title;
    
    /**
     * @VIA\Description("Статус документа")
     * @Assert\Choice({"new", "active", "completed"}, strict=true, multiple=false)
     */
    public $statusCode;
    
    /////////////////////////////////////////////

    /**
     * @VIA\Description("Идентификатор типа претензии")
     * @Assert\NotBlank(message="Идентификатор типа претензии должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $goodsIssueDocTypeId;
    
    /**
     * @VIA\Description("Номер документа")
     * @Assert\Type(type="integer")
     */
    public $number;
    
    /**
     * @VIA\Description("Идентификатор склада")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;
    
    /**
     * @VIA\Description("Идентификатор поставщика")
     * @Assert\Type(type="integer")
     */
    public $supplierId;
    
    /**
     * @VIA\Description("Описание претензии")
     * @Assert\NotBlank(message="Описание должно быть указано")
     * @Assert\Type(type="string")
     */
    public $description;
    
    /**
     * @VIA\Description("Описание состояния товара")
     * @Assert\Type(type="string")
     */
    public $goodsCondition;
    
    /**
     * @VIA\Description("Идентификатор продукта")
     * @Assert\NotBlank(message="Идентификатор продукта должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Идентификатор заказа клиента")
     * @Assert\Type(type="integer")
     */
    public $orderItemId;

    /**
     * @VIA\Description("Идентификатор заказа поставщику")
     * @Assert\NotBlank(message="Идентификатор заказа поставщику должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $supplyItemId;

    /**
     * @VIA\Description("Тип состояния товара")
     * @Assert\NotBlank(message="Тип дефекта должен быть указан")
     * @Assert\Type(type="string")
     */
    public $goodsStateCode;

    /**
     * @VIA\Description("Количество продукта")
     * @Assert\NotBlank(message="Количество продукта должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $quantity;

//    /**
//     * @VIA\Description("Список продуктов, с разбивкой по партиям и заказам")
//     * @Assert\NotBlank(message="Должен быть указан хоть один товар")
//     * @Assert\Type(type="array")
//     * @Assert\All(
//     *  @Assert\Callback({"ReservesBundle\Bus\GoodsIssueDoc\Command\CreateCommandProduct", "validate"})
//     * )
//     */
//    public $baseProducts;
//     * @Assert\All(
//     *  @Assert\Type(type="ReservesBundle\Bus\GoodsIssueDoc\Command\CreateCommandProduct"),
//     *  @Assert\Callback({"ReservesBundle\Bus\GoodsIssueDoc\Command\CreateCommandProduct", "validate"})
//     * )
    
}
