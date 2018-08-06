<?php 

namespace ReservesBundle\Bus\GoodsDecisionDocType\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;
use Doctrine\ORM\Query\ResultSetMapping;

class GoodsDecisionDocTypeItem
{
    /**
     * @VIA\Description("Идентификатор типа претензии")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Признак использования типа претензии")
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @VIA\Description("Наименование типа претензии")
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @VIA\Description("Претензия по продукту")
     * @Assert\Type(type="boolean")
     */
    public $byGoods;

    /**
     * @VIA\Description("Претензия по клиенту")
     * @Assert\Type(type="boolean")
     */
    public $byClient;

    /**
     * @VIA\Description("Претензия по поставщику")
     * @Assert\Type(type="boolean")
     */
    public $bySupplier;

    /**
     * @VIA\Description("Необходимо указывать склад")
     * @Assert\Type(type="boolean")
     */
    public $needGeoRoomId;

    /**
     * @VIA\Description("Необходимо указывать цену")
     * @Assert\Type(type="boolean")
     */
    public $needPrice;

    /**
     * @VIA\Description("Необходимо указывать идентификатор замешяющего товара")
     * @Assert\Type(type="boolean")
     */
    public $needBaseProductId;
    
    /**
     * @VIA\Description("Необходимо указывать возвращаемую сумму")
     * @Assert\Type(type="boolean")
     */
    public $needMoneyBack;

    public function __construct($id, $isActive, $name, $byGoods, $byClient, $bySupplier, $needGeoRoomId, $needBaseProductId, $needPrice, $needMoneyBack)
    {
        
        $this->id = $id;
        $this->isActive = $isActive;
        $this->name = $name;
        $this->byGoods = $byGoods;
        $this->byClient = $byClient;
        $this->bySupplier = $bySupplier;
        $this->needGeoRoomId = $needGeoRoomId;
        $this->needBaseProductId = $needBaseProductId;
        $this->needPrice = $needPrice;
        $this->needMoneyBack = $needMoneyBack;
        
    }
    
    static function getRSM()
    {
        
        $rsm = new ResultSetMapping();
       
        $rsm->addScalarResult("id", "id", "integer");
        $rsm->addScalarResult("is_active", "isActive", "boolean");
        $rsm->addScalarResult("name", "name", "string");
        $rsm->addScalarResult("by_goods", "byGoods", "boolean");
        $rsm->addScalarResult("by_client", "byClient", "boolean");
        $rsm->addScalarResult("by_supplier", "bySupplier", "boolean");
        $rsm->addScalarResult("need_geo_room_id", "needGeoRoomId", "boolean");
        $rsm->addScalarResult("need_base_product_id", "needBaseProductId", "boolean");
        $rsm->addScalarResult("need_price", "needPrice", "boolean");
        $rsm->addScalarResult("need_money_back", "needMoneyBack", "integer");
       
        return $rsm;
        
    }
    
}