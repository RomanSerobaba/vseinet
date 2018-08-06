<?php 

namespace RegisterBundle\Bus\GoodsReserveRegister\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class RemnantsList
{
    /**
     * @VIA\Description("Идентификатор склада")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;
    
    /**
     * @VIA\Description("Наименование склада")
     * @Assert\Type(type="string")
     */
    public $geoRoomName;
    
    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Наиемнование товара")
     * @Assert\Type(type="string")
     */
    public $baseProductName;

    /**
     * @VIA\Description("Идентификатор партии")
     * @Assert\Type(type="integer")
     */
    public $supplyItemId;
    
    /**
     * @VIA\Description("Заголовок партии")
     * @Assert\Type(type="string")
     */
    public $supplyTitle;
    
    /**
     * @VIA\Description("Идентификатор заказа")
     * @Assert\Type(type="integer")
     */
    public $orderItemId;
    
    /**
     * @VIA\Description("Заголовок заказа")
     * @Assert\Type(type="string")
     */
    public $orderTitle;
    
    /**
     * @VIA\Description("Код состояния товара")
     * @Assert\Type(type="string")
     */
    public $goodsConditionCode;

    /**
     * @VIA\Description("Остаток товара")
     * @Assert\Type(type="integer")
     */
    public $quantity;

}