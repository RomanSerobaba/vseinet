<?php 

namespace RegisterBundle\Bus\GoodsReserveRegister\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Item
{
    /**
     * @VIA\Description("Идентификатор строки регистра")
     * @Assert\Type(type="integer")
     */
    public $id;
    
    /**
     * @VIA\Description("Время регистрации операции")
     * @Assert\Type(type="datetime", message="Значение registeredAt должно быть датой")
     */
    public $registeredAt;
    
    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Наиемнование товара")
     * @Assert\Type(type="string")
     */
    public $productName;

    /**
     * @VIA\Description("Состояние товара")
     * @Assert\Choice({"free", "reserved", "issued"}, strict=true)
     */
    public $goodsCondition;
    
    /**
     * @VIA\Description("Идентификатор склада")
     * @Assert\Type(type="integer")
     */
    public $roomId;
    
    /**
     * @VIA\Description("Название склада")
     * @Assert\Type(type="integer")
     */
    public $roomName;
    
    /**
     * @VIA\Description("Идентификатор партии")
     * @Assert\Type(type="integer")
     */
    public $supplyItemId;
    
    /**
     * @VIA\Description("Заголовок родительского документа партии")
     * @Assert\Type(type="string")
     */
    public $supplyItemParentDocTitle;
    
    /**
     * @VIA\Description("Идентификатор позиции заказа")
     * @Assert\Type(type="integer")
     */
    public $orderItemId;

    /**
     * @VIA\Description("Заголовк заказа")
     * @Assert\Type(type="string")
     */
    public $orderTitle;

    /**
     * @VIA\Description("Изменение количества")
     * @Assert\Type(type="integer")
     */
    public $delta;

    /**
     * @VIA\Description("Заголовок родительского документа")
     * @Assert\Type(type="string")
     */
    public $parentDocTitle;

    public function __construct($id, $registeredAt, $baseProductId, $productName, $goodsCondition, $roomId, $roomName, $supplyItemId, $supplyItemParentDocTitle, $orderItemId, $orderTitle, $delta, $parentDocTitle)
    {
        $this->id = $id;
        $this->registeredAt = $registeredAt;
        $this->baseProductId = $baseProductId;
        $this->productName = $productName;
        $this->goodsCondition = $goodsCondition;
        $this->roomId = $roomId;
        $this->roomName = $roomName;
        $this->supplyItemId = $supplyItemId;
        $this->supplyItemParentDocTitle = $supplyItemParentDocTitle;
        $this->orderItemId = $orderItemId;
        $this->orderTitle = $orderTitle;
        $this->delta = $delta;
        $this->parentDocTitle = $parentDocTitle;
    }
}