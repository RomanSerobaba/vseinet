<?php 

namespace SupplyBundle\Bus\Order\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;


class OrderItems
{
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("ID")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("ид товара")
     */
    public $baseProductId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("номер позиции")
     */
    public $orderItemId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("номер заказа")
     */
    public $orderId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("цена закупки")
     */
    public $purchasePrice;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("цена продажи")
     */
    public $retailPrice;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("требуемое количество")
     */
    public $needQuantity;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("зарезервированное количество")
     */
    public $reservedQuantity;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("имя клиента")
     */
    public $clientName;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("телефоны")
     */
    public $phones;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("город")
     */
    public $city;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("можно зарезервировать с наличия")
     */
    public $hasAvailableReserve;

    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("есть комментарии")
     */
    public $hasComments;
}