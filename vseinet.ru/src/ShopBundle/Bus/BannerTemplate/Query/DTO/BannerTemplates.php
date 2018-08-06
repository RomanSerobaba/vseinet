<?php 

namespace ShopBundle\Bus\BannerTemplate\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class BannerTemplates
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
     * BannerTemplates constructor.
     * @param $id
     * @param $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}