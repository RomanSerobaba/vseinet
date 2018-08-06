<?php 

namespace OrderBundle\Bus\Data\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class GetByFilterQuery extends Message
{
    /**
     * @VIA\Description("Идентификатор заказа")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Заказ создан после")
     * @Assert\Type(type="datetime")
     */
    public $createdFrom;

    /**
     * @VIA\Description("Заказ создан до")
     * @Assert\Type(type="datetime")
     */
    public $createdTo;

    /**
     * @VIA\Description("Последний статус позиции после")
     * @Assert\Type(type="datetime")
     */
    public $lastStatusFrom;

    /**
     * @VIA\Description("Последний статус позиции до")
     * @Assert\Type(type="datetime")
     */
    public $lastStatusTo;

    /**
     * @VIA\Description("Идентификатор менеджера")
     * @Assert\Type(type="integer")
     */
    public $managerId;

    /**
     * @VIA\Description("Тип оплаты")
     * @Assert\Type(type="string")
     */
    public $paymentType;

    /**
     * @VIA\Description("Особое")
     * @Assert\Type(type="string")
     * @Assert\Choice({"special_1", "special_2", "special_3"}, strict=true)
     */
    public $special;

    /**
     * @VIA\Description("Канал продаж")
     * @Assert\Choice({"retail", "online"}, strict=true)
     */
    public $channel;

    /**
     * @VIA\Description("Способ доставки")
     * @Assert\Type(type="string")
     */
    public $deliveryType;

    /**
     * @VIA\Description("Поставщики")
     * @Assert\Type(type="array", message="Поле Поставщики должно быть формата массив чисел")
     */
    public $suppliers;

    /**
     * @VIA\Description("Статусы позиций заказа")
     * @Assert\Type(type="array", message="Поле Статусы позиций заказа должно быть формата массив строк")
     */
    public $statuses;

    /**
     * @VIA\Description("Населенные пункты")
     * @Assert\Type(type="array", message="Поле Населенные пункты должно быть формата массив чисел")
     */
    public $cities;
        
    /**
     * @VIA\Description("Идентификатор клиента")
     * @Assert\Type(type="integer")
     */
    public $clientId;

    /**
     * @VIA\Description("Номер страницы")
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(1)
     */
    public $page;
    
    /**
     * @VIA\Description("Количество элементов на странице")
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(50)
     */
    public $limit;
}