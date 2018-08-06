<?php 

namespace ContentBundle\Bus\ColorPalette\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ColorPalettes
{
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\ColorPalette\DTO\ColorPalette>")
     */
    public $palettes = [];

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\ColorPalette\DTO\Color>")
     */
    public $colors = [];


    public function __construct($palettes, $colors) 
    {
        $this->palettes = array_values($palettes);
        $this->colors = array_values($colors);
    }
}
