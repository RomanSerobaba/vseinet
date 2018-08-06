<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class FoundResults
{
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\BaseProduct\Query\DTO\FoundCategory>")
     */
    public $categories;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\BaseProduct\Query\DTO\FoundBaseProduct>")
     */
    public $products;
    

    public function __construct($categories = [], $products = [])
    {
        $this->categories = array_values($categories);
        $this->products = array_values($products);
    }
}