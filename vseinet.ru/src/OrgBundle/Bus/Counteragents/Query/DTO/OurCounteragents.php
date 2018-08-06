<?php 

namespace OrgBundle\Bus\Counteragents\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class OurCounteragents
{
    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("ID")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     * @VIA\Description("название")
     */
    public $name;

    /**
     * OurCounteragents constructor.
     * @param $id
     * @param $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}