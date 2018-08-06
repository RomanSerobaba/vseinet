<?php 

namespace ReservesBundle\Bus\GoodsReleaseDocItem\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор документа выдачи товара покупателю")
     * @Assert\NotBlank(message="Идентификатор изменяемого документа комплектации/разкомплектации должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $goodsReleaseId;

    /**
     * @VIA\Description("Количество товара к выдаче")
     * @Assert\NotBlank(message="Количество товара к выдаче должно быть заполнено")
     * @Assert\Type(type="integer")
     */
    public $initialQuantity;

    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\NotBlank(message="Идентификатор товара должен быть заполнен")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Идентификатор паллеты")
     * @Assert\Type(type="integer")
     */
    public $goodsPalletId;

    /**
     * @VIA\Description("Идентификатор строки доумента")
     * @Assert\NotBlank(message="Идентификатор заказа клиента должен быть заполнен")
     * @Assert\Type(type="integer")
     */
    public $orderItemId;
    
    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}