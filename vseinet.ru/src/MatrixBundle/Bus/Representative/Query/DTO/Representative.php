<?php 

namespace MatrixBundle\Bus\Representative\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Representative
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
     * @Assert\Type(type="date")
     */
    public $limit;

    /**
     * @Assert\Type(type="date")
     */
    public $reserve;

    /**
     * @Assert\Type(type="array")
     */
    public $templatesIds;

    public function __construct($id, $name, $limit = null, $reserve = null, $templatesJson = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->limit = $limit;
        $this->reserve = $reserve;
        $this->templatesIds = json_decode($templatesJson, true);
    }
}