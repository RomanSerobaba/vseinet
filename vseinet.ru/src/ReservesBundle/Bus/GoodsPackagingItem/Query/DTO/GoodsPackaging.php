<?php 

namespace ReservesBundle\Bus\GoodsPackagingItem\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class GoodsPackaging
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор документа")
     */
    private $goodsPackagingId;

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
     * @VIA\Description("Вес в рассчете цены")
     */
    private $quantityPerOne;
    
    public function __construct($goodsPackagingId, $baseProductId, $baseProductName, $quantityPerOne)
    {
        $this->goodsPackagingId = $goodsPackagingId;
        $this->baseProductId = $baseProductId;
        $this->baseProductName = $baseProductName;
        $this->quantityPerOne = $quantityPerOne;
    }
}