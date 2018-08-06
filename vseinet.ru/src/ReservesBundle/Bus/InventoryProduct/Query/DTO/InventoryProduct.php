<?php

namespace ReservesBundle\Bus\InventoryProduct\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class InventoryProduct
{
    /**
     * @VIA\Description("Идентификатор документа")
     * @Assert\Type(type="integer")
     */
    public $inventoryId;

    /**
     * @VIA\Description("Идентификатор категории товара первого уровня")
     * @Assert\Type(type="integer")
     */
    public $categoryIdLevel1;

    /**
     * @VIA\Description("Идентификатор категории товара второго уровня")
     * @Assert\Type(type="integer")
     */
    public $categoryIdLevel2;

    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Наименование товара")
     * @Assert\Type(type="string");
     */
    public $name;

    /**
     * @VIA\Description("Количество товара по факту")
     * @Assert\Type(type="integer")
     */
    public $initialQuantity;

    /**
     * @VIA\Description("Количество товара по факту")
     * @Assert\Type(type="integer")
     */
    public $foundQuantity;

    /**
     * @VIA\Description("Средняя закупочная цена")
     * @Assert\Type(type="integer")
     */
    public $purchasePrice;

    /**
     * @VIA\Description("Цена на сайте")
     * @Assert\Type(type="integer")
     */
    public $retailPrice;
 
    /**
     * @VIA\Description("Список количества товара подсчитанного каждым участниками в отдельности")
     * @Assert\Type(type="array<ReservesBundle\Bus\InventoryProduct\Query\DTO\FoundQuantityByParticipants>")
     */
    public $foundQuantityByParticipants;
    
    public function setFoundQuantityByParticipants($param)
    {
        if (strlen($param) > 2) {
            $array = json_decode($param);
            $this->foundQuantityByParticipants = [];
            foreach ($array as $value) {
                $this->foundQuantityByParticipants[] = new FoundQuantityByParticipants($value->id, $value->count);
            }
        }else{
            $this->foundQuantityByParticipants = [];
        }
    }

    public function __construct(int $inventoryId, $categoryIdLevel1, $categoryIdLevel2, int $id, $name, $initialQuantity, $foundQuantity, $purchasePrice, $retailPrice, $foundQuantityByParticipants = [])
    {
        $this->inventoryId = $inventoryId;
        $this->categoryIdLevel1 = $categoryIdLevel1;
        $this->categoryIdLevel2 = $categoryIdLevel2;
        $this->id = $id;
        $this->name = $name;
        $this->initialQuantity = $initialQuantity;
        $this->foundQuantity = $foundQuantity;
        $this->purchasePrice = $purchasePrice;
        $this->retailPrice = $retailPrice;
        $this->foundQuantiryByParticipants = $foundQuantityByParticipants;
    }
}