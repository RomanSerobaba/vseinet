<?php 

namespace ContentBundle\Bus\Brand\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class AlphabetIndex
{    
    /**
     * @VIA\Description("Первая буква")
     * @Assert\Type(type="string")
     */
    public $firstLetter;

    /**
     * @VIA\Description("Количество товаров") 
     * @Assert\Type(type="integer")
     */
    public $countProducts;


    public function __construct($firstLetter, $countProducts)
    {
        $this->firstLetter = $firstLetter;
        $this->countProducts = intval($countProducts);
    }
}