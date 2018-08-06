<?php 

namespace ContentBundle\Bus\ColorPalette\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Color
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $paletteId;

    /**
     * @Assert\Type(type="string")
     */
    private $valueHex;

    /**
     * @Assert\Type(type="string")
     */
    private $nameMale;

    /**
     * @Assert\Type(type="string")
     */
    private $nameFemale;

    /**
     * @Assert\Type(type="string")
     */
    private $nameNeuter;

    /**
     * @Assert\Type(type="string")
     */
    private $nameAblative;

    /**
     * @Assert\Type(type="string")
     */
    private $namePlural;

    /**
     * @Assert\Type(type="boolean")
     */
    private $isBase;


    public function __construct($id, $paletteId, $valueHex, $nameMale, $nameFemale, $nameNeuter, $nameAblative, $namePlural, $isBase) 
    {
        $this->id = $id;
        $this->paletteId = $paletteId;
        $this->valueHex = $valueHex;
        $this->nameMale = $nameMale;
        $this->nameFemale = $nameFemale;
        $this->nameNeuter = $nameNeuter;
        $this->nameAblative = $nameAblative;
        $this->namePlural = $namePlural;
        $this->isBase = $isBase;
    }
}
