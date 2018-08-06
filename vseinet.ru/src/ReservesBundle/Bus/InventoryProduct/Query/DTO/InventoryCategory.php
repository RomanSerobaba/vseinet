<?php

namespace ReservesBundle\Bus\InventoryProduct\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class InventoryCategory
{
    /**
     * @VIA\Description("Идентификатор категории")
     * @Assert\Type(type="integer")
     */
    private $id;

    /**
     * @VIA\Description("Наименование категории")
     * @Assert\Type(type="string")
     */
    private $name;

    /**
     * @VIA\Description("Дочерние категории")
     * @Assert\Type(type="array<integer>")
     */
    private $categoriesIds;

    /**
     * @VIA\Description("Товары категории")
     * @Assert\Type(type="array<integer>")
     */
    private $productsIds;


    public function __construct(int $id, $name, $categoriesIds = [], $productsIds = [])
    {
        $this->name = $id;
        $this->id = $name;
        $this->categoriesIds = $categoriesIds;
        $this->productsIds = $productsIds;
    }
}