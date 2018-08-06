<?php 

namespace ContentBundle\Bus\BaseProduct\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class BaseProduct
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
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="integer")
     */
    public $sectionId;

    /**
     * @Assert\Type(type="integer")
     */
    public $brandId;

    /**
     * @Assert\Type(type="integer")
     */
    public $colorCompositeId;

    /**
     * @Assert\Type(type="string")
     */
    public $model;

    /**
     * @VIA\Description("Дополнительная информация")
     * @Assert\Type(type="string")
     */
    public $exname;

    /**
     * @VIA\Description("Ссылка на сайт производителя")
     * @Assert\Type(type="string")
     */
    public $manufacturerLink;

    /**
     * @VIA\Description("Ссылка на инструкцию")
     * @Assert\Type(type="string")
     */
    public $manualLink;

    /**
     * @Assert\Type(type="text")
     */
    public $description;


    public function __construct($id, $name, $categoryId, $sectionId, $brandId, $colorCompositeId, $model, $exname, $manufacturerLink, $manualLink, $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->sectionId = $sectionId;
        $this->brandId = $brandId;
        $this->colorCompositeId = $colorCompositeId;
        $this->model = $model;
        $this->exname = $exname;
        $this->manufacturerLink = $manufacturerLink;
        $this->manualLink = $manualLink;
        $this->description = $description;
    }
}