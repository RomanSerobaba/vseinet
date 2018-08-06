<?php 

namespace ContentBundle\Bus\ParserSource\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Source
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $supplierId;

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
     * @Assert\Type(type="boolean")
     */
    public $useAntiGuard;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isActive;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isParseImages;


    public function __construct($id, $supplierId, $code, $alias, $url, $useAntiGuard, $isActive, $isParseImages)
    {
        $this->id = $id;
        $this->supplierId = $supplierId;
        $this->code = $code;
        $this->alias = $alias;
        $this->useAntiGuard = $useAntiGuard;
        $this->url = $url;
        $this->isActive = $isActive;
        $this->isParseImages = $isParseImages;
    }
}