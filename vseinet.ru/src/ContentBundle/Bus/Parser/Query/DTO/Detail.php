<?php 

namespace ContentBundle\Bus\Parser\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Detail
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
    public $groupId;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isHidden;


    public function __construct($id, $name, $groupId, $isHidden)
    {
        $this->id = $id;
        $this->name = $name;
        $this->groupId = $groupId;
        $this->isHidden = $isHidden;
    }
}