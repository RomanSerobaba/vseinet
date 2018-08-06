<?php 

namespace ContentBundle\Bus\GeoRoom\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ListQuery extends Message 
{
    
    /**
     * @VIA\Description("Показывать помещения/комнаты с типами из списка.
     * Массив в формате json: [""office"",""shop"",""warehouse""]")
     * @Assert\Choice({"office", "shop", "warehouse"}, multiple=true, strict=true)
     */
    public $inGeoRoomTypes;
    
    /**
     * @VIA\Description("Показывать помещения/комнаты принадлежащие списку строений/сооружений.
     * Массив в формате json: [1,2,3]")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inGeoPoints;
    
    /**
     * @VIA\Description("Номер страницы")
     * @Assert\Type(type="integer")
     */
    public $page;
    
    /**
     * @VIA\Description("Длинна страницы")
     * @Assert\Type(type="integer")
     */
    public $limit;
    
}