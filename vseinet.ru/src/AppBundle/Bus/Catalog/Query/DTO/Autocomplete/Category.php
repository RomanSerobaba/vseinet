<?php 

namespace AppBundle\Bus\Catalog\Query\DTO\Autocomplete;

use Symfony\Component\Validator\Constraints as Assert;

class Category
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
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @Assert\Type(type="array")
     */
    public $breadcrumbs;

    /**
     * @Assert\Type(type="string")
     */
    public $type = 'category';


    public function __construct($id, $name, $pid) {
        $this->id = $id;
        $this->name = $name;
        $this->pid = $pid;
    }
}
