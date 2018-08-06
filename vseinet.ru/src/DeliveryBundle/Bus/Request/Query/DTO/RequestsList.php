<?php 

namespace DeliveryBundle\Bus\Request\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class RequestsList
{
    /**
     * @Assert\Type(type="array<MatrixBundle\Bus\Template\Query\DTO\Category>")
     */
    public $items;

    /**
     * @Assert\Type(type="integer")
     */
    public $total;

    public function __construct($items, $total = 0)
    {
        $this->items = $items;
        $this->total = $total ? : 0;
    }
}