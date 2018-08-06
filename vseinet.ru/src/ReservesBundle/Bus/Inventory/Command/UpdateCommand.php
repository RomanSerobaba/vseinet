<?php 

namespace ReservesBundle\Bus\Inventory\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{    
    /**
     * @Assert\NotBlank(message="Идентификатор изменяемого документа инвентаризации должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Заголовок")
     * @Assert\NotBlank(message="Заголовок инвентаризации должен быть указан")
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
     * @Assert\NotBlank(message="Статус документа инвентаризации должен быть указан")
     * @Assert\Choice({"started", "stopped", "completed"}, strict=true)
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

}