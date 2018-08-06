<?php 

namespace ReservesBundle\Bus\GoodsPallet\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    
    /**
     * @VIA\Description("Человеночитаемое обозначение паллеты")
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @VIA\Description("Идентификатор помещения назначения")
     * @Assert\NotBlank(message="Идентификатор помещения назначения должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $geoPointId;

    /**
     * @VIA\Description("Количество товара на выдачу")
     * @Assert\Type(type="integer")
     */
    public $status;
    
    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}