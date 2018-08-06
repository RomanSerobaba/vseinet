<?php 

namespace ContentBundle\Bus\Brand\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Brand
{    
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @VIA\Description("Путь к логотипу")
     * @Assert\Type(type="string")
     */
    public $logo;

    /**
     * @VIA\Description("Сайт")
     * @Assert\Type(type="string")
     */
    public $url;

    /**
     * @VIA\Description("Запрещен к показу")
     * @Assert\Type(type="boolean")
     */
    public $isForbidden;

    /**
     * @VIA\Description("Количество товаров") 
     * @Assert\Type(type="integer")
     */
    public $countProducts;

    /**
     * @VIA\Description("Псевдонимы") 
     * @Assert\Type(type="array<ContentBundle\Entity\BrandPseudo>")
     */
    public $pseudos;


    public function __construct($id, $name, $logo, $url, $isForbidden, $countProducts)
    {
        $this->id = $id;
        $this->name = $name;
        $this->logo = $logo;
        $this->url = $url;
        $this->isForbidden = $isForbidden;
        $this->countProducts = intval($countProducts);
    }
}