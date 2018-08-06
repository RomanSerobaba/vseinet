<?php 

namespace ReservesBundle\Bus\GoodsIssueDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DocumentHead
{
    use \DocumentBundle\Prototipe\DocumentDTO;
 
    /**
     * @VIA\Description("Идентификатор типа претензии")
     * @Assert\Type(type="integer")
     */
    public $goodsIssueDocTypeId;
 
    /**
     * @VIA\Description("Наиемнование типа претензии")
     * @Assert\Type(type="string")
     */
    public $goodsIssueDocTypeName;
    
    /**
     * @VIA\Description("Cтатус документа")
     * @Assert\Type(type="string")
     */
    public $statusCode;

    /**
     * @VIA\Description("Количество не решённого по товару")
     * @Assert\Type(type="integer")
     */
    public $sumGoods;

    /**
     * @VIA\Description("Количество не решённого по клиенту")
     * @Assert\Type(type="integer")
     */
    public $sumClient;

    /**
     * @VIA\Description("Количество не решённого по поставщику")
     * @Assert\Type(type="integer")
     */
    public $sumSupplier;

    /**
     * @VIA\Description("Описание претензии")
     * @Assert\Type(type="string")
     */
    public $description;
    
    /**
     * @VIA\Description("Описание состояния товара")
     * @Assert\Type(type="string")
     */
    public $productCondition;
    
    /**
     * @VIA\Description("Cклад")
     * @Assert\Type(type="integer")
     */
    public $geoRoom;
    
    /**
     * @VIA\Description("Идентификатор поставщика")
     * @Assert\Type(type="integer")
     */
    public $supplierId;
    
    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;
    
    /**
     * @VIA\Description("Наименование товара")
     * @Assert\Type(type="string")
     */
    public $baseProductName;
    
    /**
     * @VIA\Description("Состояние товара")
     * @Assert\Type(type="string")
     */
    public $goodsStateCode;
    
    /**
     * @VIA\Description("Количество товара")
     * @Assert\Type(type="integer")
     */
    public $quantity;
    
    /**
     * @VIA\Description("Номер заказа клиента")
     * @Assert\Type(type="integer")
     */
    public $orderNumber;
    
    /**
     * @VIA\Description("Идентификатор позиции заказа клиента")
     * @Assert\Type(type="integer")
     */
    public $orderItemId;
    
    /**
     * @VIA\Description("Наиемнование заказа клиента")
     * @Assert\Type(type="string")
     */
    public $orderTitle;

    /**
     * @VIA\Description("Номер заказа поставщику")
     * @Assert\Type(type="integer")
     */
    public $supplyNumber;

    /**
     * @VIA\Description("Идентификатор позиции заказа поставщику")
     * @Assert\Type(type="integer")
     */
    public $supplyItemId;
    
    /**
     * @VIA\Description("Наименование заказа поставщику")
     * @Assert\Type(type="string")
     */
    public $supplyTitle;
    
    /**
     * @VIA\Description("Код поставщика")
     * @Assert\Type(type="string")
     */
    public $supplierCode;
    
    /**
     * @VIA\Description("Цена закупки")
     * @Assert\Type(type="integer")
     */
    public $purchasePrice;

    /**
     * @VIA\Description("Цена продажи")
     * @Assert\Type(type="integer")
     */
    public $retailPrice;
    
    public function setGeoRoom($inJson)
    {
        $inData = json_decode($inJson, true);
        if (!empty($inData)) {
            $this->geoRoom = new SimpleData($inData['id'], $inData['name']);
        }
    }
    
}