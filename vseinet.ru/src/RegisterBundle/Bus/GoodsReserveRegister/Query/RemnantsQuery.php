<?php 

namespace RegisterBundle\Bus\GoodsReserveRegister\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class RemnantsQuery extends Message 
{
    /**
     * @VIA\Description("Дата выборки")
     * @Assert\Date()
     */
    public $actualDate;
    
    /**
     * @VIA\Description("Поля для группировки")
     * @Assert\NotBlank(message="Список обрабатываемых полей должен быть определен.")
     * @Assert\Choice({"geoRoom", "baseProduct", "orderItem", "supplyItem", "goodsConditionCode"}, strict=true, multiple=true)
     */
    public $groupBy;
    
    /**
     * @VIA\Description("Список идентификаторов выбираемых товаров")
     * @Assert\Type("array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inBaseProductsIds;
    
    /**
     * @VIA\Description("Список идентификаторов выбираемых складов")
     * @Assert\Type("array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inGeoRoomsIds;

    /**
     * @VIA\Description("Список идентификаторов позиций заказов")
     * @Assert\Type("array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inOrdersItemsIds;
    
    /**
     * @VIA\Description("Список идентификаторов позиций партий")
     * @Assert\Type("array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inSypplyItemsIds;
    
    /**
     * @VIA\Description("Список допустимых состояний товара")
     * @Assert\Choice({"free", "reserved", "equipment", "issued", "releasable"}, strict=true, multiple=true)
     */
    public $inGoodsConditionsCodes;    
}