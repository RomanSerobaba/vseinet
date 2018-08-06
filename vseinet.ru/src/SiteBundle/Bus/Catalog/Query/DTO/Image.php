<?php 

namespace SiteBundle\Bus\Catalog\Query\DTO;

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


    public function __construct($id, $baseSrc)
    {
        $this->id = $id;
        $this->baseSrc = $baseSrc;
    }
}
