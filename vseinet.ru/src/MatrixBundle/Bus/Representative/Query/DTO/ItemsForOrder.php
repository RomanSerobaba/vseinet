<?php 

namespace MatrixBundle\Bus\Representative\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ItemsForOrder
{
    /**
     * @Assert\Type(type="array<MatrixBundle\Bus\Representative\Query\DTO\Category>")
     */
    public $categories;

    /**
     * @Assert\Type(type="array<MatrixBundle\Bus\Representative\Query\DTO\BaseProduct>")
     */
    public $products;

    public function __construct($categories, $products)
    {
        $this->categories = array_values($categories);
        $this->products = array_values($products);
    }
}