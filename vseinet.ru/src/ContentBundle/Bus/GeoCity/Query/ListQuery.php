<?php 

namespace ContentBundle\Bus\GeoCity\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class ListQuery extends Message 
{
    
    /**
     * @VIA\Description("Показывать города принадлежащие списку регионов.
     * Массив в формате json: [1,2,3]")
     * @Assert\Type(type="array")
     * @Assert\All(@Assert\Type(type="integer"))
     */
    public $inGeoRegiones;
    
    /**
     * @VIA\Description("Показывать города только с подчинеными конатами")
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