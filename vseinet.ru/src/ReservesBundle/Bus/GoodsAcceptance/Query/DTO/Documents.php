<?php 

namespace ReservesBundle\Bus\GoodsAcceptance\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use DocumentBundle\Prototipe\ParentDocumentDTO;
use AppBundle\Annotation as VIA;

class Documents
{
    use \DocumentBundle\Prototipe\DocumentDTO;

    /**
     * @VIA\Description("Идентификатор геоточки")
     * @Assert\Type(type="integer")
     */
    public $geoRoomId;
    
    /**
     * @VIA\Description("Наименование геоточки")
     * @Assert\Type(type="string")
     */
    public $geoRoomName;
    
    /**
     * @VIA\Description("Идентификатор геоточки отправителя")
     * @Assert\Type(type="integer")
     */
    public $geoRoomSourceId;

    /**
     * @VIA\Description("Наиемнование геоточки отправителя")
     * @Assert\Type(type="string")
     */
    public $geoRoomSourceName;

}