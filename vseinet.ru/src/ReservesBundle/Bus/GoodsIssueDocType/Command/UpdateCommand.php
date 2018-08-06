<?php 

namespace ReservesBundle\Bus\GoodsIssueDocType\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{    

    /**
     * @VIA\Description("Идентификатор типа претензии")
     * @Assert\NotBlank(message="Идентификатор типа претензии должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Признак использования типа претензии")
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @VIA\Description("Признак интерактивного использования типа претензии")
     * @Assert\Type(type="boolean")
     */
    public $isInteractive;

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
     * @VIA\Description("Помечать товар в регистре остатков как проблемный")
     * @Assert\Type(type="boolean")
     */
    public $makeIssueReserve;

}
