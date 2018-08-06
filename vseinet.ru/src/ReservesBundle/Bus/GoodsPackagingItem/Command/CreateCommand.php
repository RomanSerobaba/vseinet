<?php 

namespace ReservesBundle\Bus\GoodsPackagingItem\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    
    /**
     * @VIA\Description("Идентификатор документа комплектации/разкомплектации")
     * @Assert\NotBlank(message="Идентификатор изменяемого документа комплектации/разкомплектации должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $goodsPackagingId;

    /**
     * @VIA\Description("Идентификатор товара")
     * @Assert\NotBlank(message="Идентификатор товара должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Количество товара на одну единицу коиплекта")
     * @Assert\Type(type="integer")
     */
    public $quantityPerOne;

}