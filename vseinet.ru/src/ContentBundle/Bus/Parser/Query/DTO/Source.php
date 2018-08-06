<?php 

namespace ContentBundle\Bus\Parser\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Source
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="string")
     */
    public $code;

    /**
     * @Assert\Type(type="string")
     */
    public $alias;

    /**
     * @Assert\Type(type="string")
     */
    public $url;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $groupIds = [];


    public function __construct($id, $code, $alias, $url)
    {
        $this->id = $id;
        $this->code = $code;
        $this->alias = $alias;
        $this->url = $url;
    }
}