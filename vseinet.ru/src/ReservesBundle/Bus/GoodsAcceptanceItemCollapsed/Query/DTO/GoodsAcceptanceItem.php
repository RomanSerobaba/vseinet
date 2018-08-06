<?php 

namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GoodsAcceptanceItem
{
    
    /**
     * @VIA\Description("Идентификатор документа")
     * @Assert\Type(type="integer")
     */
    public $goodsAcceptanceId;

    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Наименование товара")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @VIA\Description("Тип позиции товар/полета (product/pallete)")
     * @Assert\Type(type="string")
     */
    public $type;
    
    /**
     * @VIA\Description("Идентификатор конечного получателя")
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @VIA\Description("Код конечного получателя")
     * @Assert\Type(type="string")
     */
    public $geoPointCode;

    /**
     * @VIA\Description("Название конечного получателя")
     * @Assert\Type(type="string")
     */
    public $geoPointName;

    /**
     * @VIA\Description("Ориентировочная цена закупки")
     * @Assert\Type(type="integer")
     */
    public $purchasePrice;

    /**
     * @VIA\Description("тип дефекта")
     * @Assert\Type(type="string")
     */
    public $goodsStateCode;
    
    /**
     * @VIA\Description("К приемке")
     * @Assert\Type(type="integer")
     */
    public $initialQuantity;
    
    /**
     * @VIA\Description("Принято")
     * @Assert\Type(type="integer")
     */
    public $quantity;
    
    public function __construct($goodsAcceptanceId, $baseProductId, $baseProductName, $geoPointId, $geoPointCode, $geoPointName, $goodsStateCode, $purchasePrice, $initialQuantity, $quantity)
    {

        $this->goodsAcceptanceId = $goodsAcceptanceId;
        $this->baseProductId = $baseProductId;
        $this->baseProductName = $baseProductName;
        $this->geoPointId = $geoPointId;
        $this->geoPointCode = $geoPointCode;
        $this->geoPointName = $geoPointName;
        $this->goodsStateCode = $goodsStateCode;
        $this->purchasePrice = $purchasePrice;
        $this->initialQuantity = $initialQuantity;
        $this->quantity = $quantity;
                
    }
    
}