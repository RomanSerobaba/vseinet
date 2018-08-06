<?php 

namespace SiteBundle\Bus\Product\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Image 
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $baseSrc;

    /**
     * @Assert\Type(type="integer")
     */
    public $width;

    /**
     * @Assert\Type(type="integer")
     */
    public $height;


    public function __construct($id, $baseSrc, $width, $height)
    {
        $this->id = $id;
        $this->baseSrc = $baseSrc;
        $this->width = $width;
        $this->height = $height;
    }
}
