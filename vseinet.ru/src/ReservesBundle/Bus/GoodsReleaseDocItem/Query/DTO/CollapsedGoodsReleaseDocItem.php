<?php 

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CollapsedGoodsReleaseDocItem
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор документа")
     */
    private $goodsReleaseId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор товара/палеты")
     */
    private $id;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование товара/палеты")
     */
    private $name;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Тип содержимого строки - product/pallete")
     */
    private $type;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("тип дефекта")
     */
    private $goodsStateCode;
    
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Отпущено")
     */
    private $quantity;
    
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Отпущено")
     */
    private $initialQuantity;
    
    public function __construct($goodsReleaseId, $id, $name, $type, $goodsStateCode, $quantity, $initialQuantity)
    {
        $this->goodsReleaseId = $goodsReleaseId;
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->goodsStateCode = $goodsStateCode;
        $this->quantity = $quantity;
        $this->initialQuantity = $initialQuantity;
    }
}