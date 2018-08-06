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
     * @VIA\Description("Идентификатор паллеты")
     * @Assert\Type(type="integer")
     */
    public $goodsPalletId;

    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\NotBlank(message="Идентификатор товара должен быть заполнен")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Количество товара на выдачу")
     * @Assert\Type(type="integer")
     */
    public $initialQuantity;

    /**
     * @VIA\Description("Выданное количество товара")
     * @Assert\Type(type="integer")
     */
    public $quantity;

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