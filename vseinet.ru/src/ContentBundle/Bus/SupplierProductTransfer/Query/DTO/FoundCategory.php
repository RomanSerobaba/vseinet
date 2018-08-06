<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class FoundCategory
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
     * @Assert\Type(type="array[]")
     */
    public $breadcrumbs = [];

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $productIds = [];
    

    public function __construct($id, $name, $pid)
    {
        $this->id = $id;
        $this->name = $name;
        $this->pid = $pid;
    }
}