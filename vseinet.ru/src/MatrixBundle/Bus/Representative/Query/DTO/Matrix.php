<?php 

namespace MatrixBundle\Bus\Representative\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Matrix
{
    /**
     * @Assert\Type(type="array<MatrixBundle\Bus\Template\Query\DTO\Category>")
     */
    public $categories;

    /**
     * @Assert\Type(type="array<MatrixBundle\Bus\Template\Query\DTO\BaseProduct>")
     */
    public $products;

    /**
     * @Assert\Type(type="integer")
     */
    public $total;

    public function __construct($categories, $products, $total)
    {
        // array_walk($categories, function($item) { $item->pid = null; });
        $this->categories = array_values($categories);
        // array_walk($products, function($item) { $item->categoryId = null; });
        $this->products = array_values($products);
        $this->total = $total ? : 0;
    }
}