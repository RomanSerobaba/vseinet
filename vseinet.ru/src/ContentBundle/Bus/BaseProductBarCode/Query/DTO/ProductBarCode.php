<?php 

namespace ContentBundle\Bus\BaseProductBarCode\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class ProductBarCode
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор штрихкода")
     */
    private $id;
    
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Штрихкод продукта")
     */
    private $barCode;
    
    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Тип штрихкода продукта")
     */
    private $barCodeType;
    
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор продукта")
     */
    private $baseProductId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование продукта")
     */
    private $baseProductName;
    
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор паллеты")
     */
    private $goodsPalletId;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Обозначение паллеты")
     */
    private $goodsPalletTitle;
    
    /**
     * @Assert\Type(type="boolean")
     * @VIA\Description("Продукт скрыт")
     */
    private $baseProductHidden;
    
    
    public function __construct($id, $barCode, $barCodeType, $baseProductId, $baseProductName, $goodsPalletId, $goodsPalletTitle, $baseProductHidden)
    {
        $this->id = $id;
        $this->barCode = $barCode;
        $this->barCodeType = $barCodeType;
        $this->baseProductId = $baseProductId;
        $this->baseProductName = $baseProductName;
        $this->goodsPalletId = $goodsPalletId;
        $this->goodsPalletTitle = $goodsPalletTitle;
        $this->baseProductHidden = $baseProductHidden;
    }
}