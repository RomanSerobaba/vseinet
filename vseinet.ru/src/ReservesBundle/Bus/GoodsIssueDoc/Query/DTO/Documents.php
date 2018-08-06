<?php 
namespace ReservesBundle\Bus\GoodsIssueDoc\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;
use AppBundle\SimpleTools\DocumentTools;

class Documents
{
    use \DocumentBundle\Prototipe\DocumentDTO;
 
    /**
     * @VIA\Description("Идентификаторов продукта")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;
    
    /**
     * @VIA\Description("Наименование продукта")
     * @Assert\Type(type="string")
     */
    public $baseProductName;
    
    /**
     * @VIA\Description("Идентификатор типа претензии")
     * @Assert\Type(type="integer")
     */
    public $goodsIssueDocTypeId;
 
    /**
     * @VIA\Description("Наименование типа претензии")
     * @Assert\Type(type="string")
     */
    public $goodsIssueDocTypeName;
    
    /**
     * @VIA\Description("Cтатус документа")
     * @Assert\Type(type="string")
     */
    public $statusCode;

    /**
     * @VIA\Description("Cклад")
     * @Assert\Type(type="ReservesBundle\Bus\GoodsIssueDoc\Query\DTO\SimpleData")
     */
    public $geoRoom;

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