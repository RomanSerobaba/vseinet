<?php 

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GoodsReleaseDocItem
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор позиции (строки) документа")
     */
    private $id;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор документа")
     */
    private $goodsReleaseId;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор товара")
     */
    private $baseProductId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование товара")
     */
    private $baseProductName;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор паллеты")
     */
    private $goodsPalletId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Название паллеты")
     */
    private $goodsPalletTitle;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Позиция заказа клиента")
     */
    private $orderItemId;

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
    
    public function __construct($id, $goodsReleaseId, $baseProductId, $baseProductName, $goodsPalletId, $goodsPalletTitle, $orderItemId, $goodsStateCode, $quantity, $initialQuantity)
    {
        $this->id = $id;
        $this->goodsReleaseId = $goodsReleaseId;
        $this->baseProductId = $baseProductId;
        $this->baseProductName = $baseProductName;
        $this->goodsPalletId = $goodsPalletId;
        $this->goodsPalletTitle = $goodsPalletTitle;
        $this->orderItemId = $orderItemId;
        $this->goodsStateCode = $goodsStateCode;
        $this->quantity = $quantity;
        $this->initialQuantity = $initialQuantity;
    }
}