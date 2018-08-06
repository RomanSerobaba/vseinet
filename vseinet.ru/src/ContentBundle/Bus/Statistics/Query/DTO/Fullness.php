<?php 

namespace ContentBundle\Bus\Statistics\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Fullness
{    
    /**
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="string")
     */
    public $categoryName;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Количество заполненных товаров")
     */
    public $count;

    /**
     * @Assert\Type(type="integer")
     * @VIA\Description("Количество заполненных товаров из парсера")
     */
    public $countFromParser;

    /**
     * @Assert\Type(type="float")
     * @VIA\Description("Процент заполненности")
     */
    public $percentFullness;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isLeaf;


    public function __construct($categoryId, $categoryName, $count, $countFromParser, $percentFullness, $isLeaf)
    {
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->count = $count;
        $this->countFromParser = $countFromParser;
        $this->percentFullness = $percentFullness;
        $this->isLeaf = $isLeaf;
    }
}