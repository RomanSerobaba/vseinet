<?php

namespace ReservesBundle\Bus\GoodsAcceptanceItemCollapsed\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class DeleteCommand extends Message
{

    /**
     * @VIA\Description("Идентификатор документа")
     * @Assert\NotBlank(message="Идентификатор изменяемого документа должен быть заполнен")
     * @Assert\GreaterThan(0)
     * @Assert\Type(type="integer")
     */
    public $goodsAcceptanceId;

    /**
     * @VIA\Description("Идентификатор продукта")
     * @Assert\NotBlank(message="Идентификатор продукта должен быть заполнен")
     * @Assert\GreaterThan(0)
     * @Assert\Type(type="integer")
     */
    public $baseProductId;

    /**
     * @VIA\Description("Код состояния товара")
     * @Assert\Type(type="string")
     * @Assert\GreaterThan(0)
     * @VIA\DefaultValue("normal")
     */
    public $goodsStateCode;

    /**
     * @VIA\Description("Идентификатор направления")
     * @Assert\NotBlank(message="Идентификатор направления должен быть заполнен")
     * @Assert\GreaterThan(0)
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

}