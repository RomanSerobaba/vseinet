<?php 

namespace ContentBundle\Bus\Template\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ParserSource
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
     * @Assert\Type(type="string")
     */
    public $supplier;

    /**
     * @Assert\Type(type="integer")
     */
    public $countDetails;

    /**
     * @Assert\Type(type="integer")
     */
    public $countNotAttachedDetails;


    public function __construct($id, $code, $alias, $url, $supplier, $countDetails, $countAttachedDetails)
    {
        $this->id = $id;
        $this->code = $code;
        $this->alias = $alias;
        $this->url = $url;
        $this->supplier = $supplier;
        $this->countDetails = $countDetails;
        $this->countNotAttachedDetails = $countDetails - $countAttachedDetails;
    }
}