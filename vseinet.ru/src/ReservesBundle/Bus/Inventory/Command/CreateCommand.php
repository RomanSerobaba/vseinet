<?php 

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    

    /**
     * @VIA\Description("Заголовок")
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @VIA\Description("Место проведения инвентаризации")
     * @Assert\NotBlank(message="Место проведения инвентаризации должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @VIA\Description("Ответственный за проведение инвентаризации")
     * @Assert\NotBlank(message="Ответственный за проведение инвентаризации должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $responsibleId;

    /**
     * @VIA\Description("Статус документа инвентаризации")
     * @Assert\Choice({"created", "started"}, strict=true)
     * @VIA\DefaultValue("started")
     */
    public $status;

    /**
     * @VIA\Description("Строка идентификаторов категорий товара для проведения инвентаризации в виде json массива - [1,2,3,4]")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $categories;
    
    /**
     * @VIA\Description("Строка идентификаторов участников инвентаризации в виде json массива - [1,2,3,4]")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $participants;

    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}