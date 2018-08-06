<?php 

namespace ContentBundle\Bus\BaseProductBarCode\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class FindByBarcodeResult
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор элемента")
     */
    private $id;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Наименование элемента")
     */
    private $name;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Тип элемента pallet/product")
     */
    private $type;

    public function __construct($goodsPalletId = null, $goodsPalletTitle, $baseProductId = null, $baseProductName)
    {
        $this->id = (empty($goodsPalletId) ? $baseProductId : $goodsPalletId);
        $this->name = (empty($goodsPalletId) ? $baseProductName : $goodsPalletTitle);
        $this->type = (empty($goodsPalletId) ? 'product' : 'pallet');
    }
}