<?php 

namespace GeoBundle\Service\DTO;

use AppBundle\Annotation as VIA;
use Symfony\Component\Validator\Constraints as Assert;

class Region
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
     * Region constructor.
     * @param $id
     * @param $name
     */
    public function __construct($id=null, $name=null)
    {
        $this->id = $id;
        $this->name = $name;
    }
}