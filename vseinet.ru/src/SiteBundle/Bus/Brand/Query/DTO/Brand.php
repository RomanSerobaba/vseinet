<?php 

namespace SiteBundle\Bus\Brand\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Type(type="string")
     */
    public $url;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isForbidden;


    public function __construct($id, $name, $url, $isForbidden) {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->isForbidden = $isForbidden;
    }
}
