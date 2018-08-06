<?php 

namespace ReservesBundle\Bus\GoodsDecisionDocType\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    
    /**
     * @VIA\Description("Признак использования типа претензии")
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @VIA\Description("Наименование типа претензии")
     * @Assert\NotBlank(message="Наименование типа претензии должено быть указано")
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

    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}