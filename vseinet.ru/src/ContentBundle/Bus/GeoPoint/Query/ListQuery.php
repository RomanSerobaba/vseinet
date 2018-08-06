<?php 

namespace ContentBundle\Bus\GeoPoint\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ListQuery extends Message 
{
    
    /**
     * @VIA\Description("Показывать строения/сооружения с типами из списка.
     * Массив в формате json: [""service"",""supplier"",""representative""]")
     * @Assert\Choice({"service", "supplier", "representative"}, multiple=true, strict=true)
     */
    public $inGeoPointTypes;
    
    /**
     * @VIA\Description("Показывать строения/сооружения принадлежащие списку городов.
     * Массив в формате json: [1,2,3]")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inGeoCities;
    
    /**
     * @VIA\Description("Показывать строения/сооружения только с подчинеными конатами")
     * @Assert\Type(type="boolean")
     */
    public $onlyWithGeoRoom;
    
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