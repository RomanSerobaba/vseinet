<?php 

namespace ReservesBundle\Bus\GoodsAcceptance\Command;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class CreateCommand extends Message
{    

    /**
     * @VIA\Description("Уникальный идентификатор документа родителя")
     * @Assert\Type(type="integer")
     */
    public $parentDocumentId;

    /**
     * @VIA\Description("Человекочитаемый заголовок")
     * @Assert\Type(type="string")
     */
    public $title;
    
    ////////////////////////////////////////////////////////////////////////
    
    /**
     * @VIA\Description("Склад - приемник")
     * @Assert\NotBlank(message="Конечное место хранения товаров должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

    /**
     * @VIA\Description("Склад - источник")
     * @Assert\NotBlank(message="Начальное место хранения товаров должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $geoRoomSource;

    ////////////////////////////////////////////////////////////////////////

    /**
     * @Assert\Uuid
     */
    public $uuid;
    
}