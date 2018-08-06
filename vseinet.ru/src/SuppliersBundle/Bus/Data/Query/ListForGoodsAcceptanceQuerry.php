<?php 

namespace SuppliersBundle\Bus\Data\Query;

use AppBundle\Bus\Message\Message;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class ListForGoodsAcceptanceQuerry extends Message
{
    
    /**
     * @VIA\Description("Склад - приемник")
     * @Assert\NotBlank(message="Конечное место хранения товаров должно быть указано")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;

}