<?php 

namespace FinanseBundle\Bus\FinancialCounteragent\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class FinancialCounteragentDTO
{
    
    /**
     * @VIA\Description("Идентификатор Контрагента")
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @VIA\Description("Наименование контрагента")
     * @Assert\Type(type="string")
     */
    public $name;

}