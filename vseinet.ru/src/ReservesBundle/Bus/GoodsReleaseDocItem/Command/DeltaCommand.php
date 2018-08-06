<?php 

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeltaCommand extends Message
{    
    /**
     * @VIA\Description("Уникальный идентификатор документа выдачи товара покупателю")
     * @Assert\NotBlank(message="Уникальный идентификатор документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $goodsReleaseId;

    /**
     * @VIA\Description("Тип позиции: товар/паллета")
     * @Assert\NotBlank(message="Тип позиции должен быть указан")
     * @Assert\Choice({"product", "pallet"}, strict=true, multiple=false)
     */
    public $type;

    /**
     * @VIA\Description("Идентификатор продукта/паллеты")
     * @Assert\NotBlank(message="Идентификатор продукта/паллеты должен быть заполнен")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Тип дефекта (особых условий) товара")
     * @Assert\Type(type="string")
     * @VIA\DefaultValue("normal")
     */
    public $goodsStateCode;
    
    /**
     * @VIA\Description("Количество обрабатываемого товара")
     * @Assert\NotBlank(message="Количество обрабатываемого товара должно быть заполнено")
     * @Assert\NotNull(message="Количество обрабатываемого товара должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $delta;

}
