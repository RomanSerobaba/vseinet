<?php 

namespace OrderBundle\Bus\Data\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AnnulCause
{
    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * AnnulCause constructor.
     * @param $code
     * @param $name
     */
    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }
}