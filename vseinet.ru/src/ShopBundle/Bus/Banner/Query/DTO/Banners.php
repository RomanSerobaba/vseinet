<?php 

namespace ShopBundle\Bus\Banner\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Banners
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $type;

    /**
     * @Assert\Type(type="integer")
     */
    public $weight;

    /**
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isVisible;

    /**
     * @Assert\Type(type="boolean")
     */
    public $tabIsFixed;

    /**
     * Banners constructor.
     * @param $id
     * @param $type
     * @param $weight
     * @param $title
     * @param $isVisible
     * @param $tabIsFixed
     */
    public function __construct($id, $type, $weight, $title, $isVisible, $tabIsFixed)
    {
        $this->id = $id;
        $this->type = $type;
        $this->weight = $weight;
        $this->title = $title;
        $this->isVisible = $isVisible;
        $this->tabIsFixed = $tabIsFixed;
    }
}