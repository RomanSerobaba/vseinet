<?php 

namespace ReservesBundle\Bus\GoodsPallet\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{    

    /**
     * @VIA\Description("Идентификатор")
     * @Assert\NotBlank(message="Идентификатор должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Человеночитаемое обозначение паллеты")
     * @Assert\NotBlank(message="Текст должен быть указан")
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
     * @VIA\Description("Статус паллеты")
     * @Assert\NotBlank(message="Статус паллеты должен быть указан")
     * @Assert\Type(type="string")
     */
    public $status;
    
}