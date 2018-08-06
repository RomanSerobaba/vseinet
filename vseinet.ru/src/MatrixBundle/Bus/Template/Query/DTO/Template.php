<?php 

namespace MatrixBundle\Bus\Template\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Template
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="date")
     */
    public $activeFrom;

    /**
     * @Assert\Type(type="date")
     */
    public $activeTill;

    public function __construct($id, $name, $activeFrom = null, $activeTill = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->activeFrom = $activeFrom;
        $this->activeTill = $activeTill;
    }
}