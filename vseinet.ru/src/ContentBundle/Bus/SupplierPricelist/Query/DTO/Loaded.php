<?php 

namespace ContentBundle\Bus\SupplierPricelist\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Loaded
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="datetime")
     */
    public $uploadStartedAt;


    public function __construct($id, $code, $name, $uploadStartedAt)
    {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->uploadStartedAt = $uploadStartedAt;
    }
}