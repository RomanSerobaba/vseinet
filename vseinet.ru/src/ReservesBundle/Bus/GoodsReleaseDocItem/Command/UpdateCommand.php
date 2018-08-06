<?php 

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор документа выдачи товара покупателю")
     * @Assert\NotBlank(message="Идентификатор изменяемого документа комплектации/разкомплектации должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $goodsReleaseId;

    /**
     * @VIA\Description("Идентификатор строки доумента")
     * @Assert\NotBlank(message="Идентификатор стороки документа должен быть заполнен")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Количество отпускаемого товара")
     * @Assert\NotBlank(message="Количество отпускаемого товара должно быть заполнено")
     * @Assert\Type(type="integer")
     */
    public $quantity;

    /**
     * @VIA\Description("Тип дефекта (особых условий) товара")
     * @Assert\Type(type="string")
     */
    public $defectType;

}