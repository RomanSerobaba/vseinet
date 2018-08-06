<?php 

namespace FinanseBundle\Bus\FinancialCounteragent\Query;

use AppBundle\Bus\Message\Message;
use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class FoundResultsQuery extends Message 
{
    /**
     * @VIA\Description("Показывать физические лица")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(true)
     */
    public $withIndividual;
    
    /**
     * @VIA\Description("Показывать юридические лица")
     * @Assert\Type(type="boolean")
     * @VIA\DefaultValue(true)
     */
    public $withLegal;
    
    /**
     * @VIA\Description("Сртрока запроса/ИНН")
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