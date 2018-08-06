<?php 

namespace SupplyBundle\Bus\Order\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class OrderProducts
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("ид товара")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("код товара у поставщика")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("наименование товара у поставщика")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("путь к фото товара")
     */
    public $photoUrl;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("требуемое количество товара")
     */
    public $needQuantity;
}