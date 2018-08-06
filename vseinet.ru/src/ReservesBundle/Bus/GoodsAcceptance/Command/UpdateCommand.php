<?php 

namespace ReservesBundle\Bus\GoodsAcceptance\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class UpdateCommand extends Message
{    
    /**
     * @VIA\Description("Уникальный идентификатор изменяемого документа")
     * @Assert\NotBlank(message="Идентификатор изменяемого документа должен быть указан")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Человекочитаемый заголовок")
     * @Assert\NotBlank(message="Заголовок изменяемого документа должен быть указан")
     * @Assert\Type(type="string")
     */
    public $title;
    
    /**
     * @VIA\Description("Статус документа")
     * @Assert\NotBlank(message="Статус изменяемого документа должен быть указан")
     * @Assert\Choice({"new", "active", "completed"}, strict=true, multiple=false)
     */
    public $statusCode;

    ////////////////////////////////////////////////////////////////////////
    
    /**
     * @VIA\Description("Склад - приемник")
     * @Assert\NotBlank(message="Конечное место хранения товаров должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @VIA\Description("Склад - источник")
     * @Assert\Type(type="integer")
     */
    public $geoRoomSource;

}