<?php 

namespace RegisterBundle\Bus\GoodsReserveRegister\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class HistoryQuery extends Message 
{
    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Идентификатор помещения")
     * @Assert\Type(type="integer")
     */
    public $roomId;

    /**
     * @VIA\Description("Номер заказа")
     * @Assert\Type(type="integer")
     */
    public $orderNumber;

    /**
     * @VIA\Description("Начало периода дат операций")
     * @Assert\Type(type="datetime", message="Значение registeredFrom должно быть датой")
     */
    public $registeredFrom;

    /**
     * @VIA\Description("Конец периода дат операций")
     * @Assert\Type(type="datetime", message="Значение registeredTo должно быть датой")
     */
    public $registeredTo;

    /**
     * @VIA\Description("Состояние товара")
     * @Assert\Choice({"free", "reserved", "issued"}, strict=true)
     */
    public $productCondition;

    /**
     * @VIA\Description("Номер родительского документа")
     * @Assert\Type(type="integer")
     */
    public $parentDocNumber;

    /**
     * @VIA\Description("Тип родительского документа")
     * @Assert\Choice({"order", "goods_issue", "goods_acceptance", "goods_release", "inventory", "goods_movement", "goods_packaging", "supplier_reserve", "available_goods_reservation", "order_annul", "goods_issue_decision", "supply", "order_receipt"}, strict=true)
     */
    public $parentDocType;

    /**
     * @VIA\Description("Номер страницы")
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(1)
     */
    public $page;

    /**
     * @VIA\Description("Количество элементов на одной странице")
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(50)
     */
    public $limit;
}