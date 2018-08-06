<?php 

namespace OrgBundle\Bus\Representative\Query\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class Point
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
     * Point constructor.
     * @param integer|null $id
     * @param string|null $name
     */
    public function __construct($id=null, $name=null)
    {
        $this->id = $id;
        $this->name = $name;
    }
}