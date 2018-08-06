<?php 

namespace ContentBundle\Bus\BaseProduct\Command\DTO;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Used in RenameCommand 
 */
class BaseProduct
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
    public $brand;

    /**
     * @Assert\Type(type="string")
     */
    public $color;

    /**
     * @Assert\Type(type="string")
     */
    public $model;

    /**
     * @Assert\Type(type="string")
     */
    public $exname;
    

    public function __construct($id, $name, $brand, $colorComposite, $model, $exname)
    {
        $this->id = $id;
        $this->name = $name;
        $this->brand = $brand;
        $this->colorComposite = $colorComposite;
        $this->model = $model;
        $this->exname = $exname;
    }
}