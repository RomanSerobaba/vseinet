<?php 

namespace ContentBundle\Bus\GeoRoom\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class FoundResults
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Идентификатор комнаты")
     */
    private $id;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("Синтетическое наименование комнаты")
     */
    private $name;

    public function __construct($id, $nameRoom, $namePoint, $nameCity)
    {
        $this->id = $id;
        $this->name = $nameCity .', '. ($namePoint == $nameCity ? '' : $namePoint .', '). $nameRoom;
    }
}