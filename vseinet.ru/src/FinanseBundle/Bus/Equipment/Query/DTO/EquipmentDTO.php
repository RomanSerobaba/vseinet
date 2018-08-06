<?php 

namespace FinanseBundle\Bus\Equipment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class EquipmentDTO
{
    
    /**
     * @VIA\Description("Идентификатор оборудования")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Наименование оборудования")
     * @Assert\Type(type="string")
     */
    public $name;

}