<?php 

namespace FinanseBundle\Bus\Equipment\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class FoundResultsQuery extends Message 
{
    /**
     * @VIA\Description("Показывать машины")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(false)
     */
    public $withCar;
    
    /**
     * @VIA\Description("Показывать оборудование из товаров")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(false)
     */
    public $withProduct;
    
    /**
     * @VIA\Description("Сртрока запроса/номера машины")
     * @Assert\Type(type="string")
     */
    public $q;
    
    /**
     * @VIA\Description("Длинна страницы")
     * @Assert\Type(type="integer")
     * @VIA\DefaultValue(50)
     */
    public $limit;
    
}