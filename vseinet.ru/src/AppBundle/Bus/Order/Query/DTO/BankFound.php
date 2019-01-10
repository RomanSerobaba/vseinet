<?php

namespace AppBundle\Bus\Order\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class BankFound
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
     * @Assert\Type(type="string")
     */
    public $bic;


    public function __construct($id, $name, $bic)
    {
        $this->id = $id;
        $this->name = $name;
        $this->bic = $bic;
    }
}
