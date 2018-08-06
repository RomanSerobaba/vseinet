<?php 

namespace ContentBundle\Bus\BaseProduct\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

/**
 * @deprecated
 */
class FindFound
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}