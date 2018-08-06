<?php 

namespace ContentBundle\Bus\CategorySection\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Template
{
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\CategorySection\Query\DTO\DetailGroup>")
     */
    public $groups;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\CategorySection\Query\DTO\Detail>")
     */
    public $details;
    
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\CategorySection\Query\DTO\DetailDepend>")
     */
    public $depends;
    
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\CategorySection\Query\DTO\DetailValue>")
     */
    public $values;
    

    public function __construct($groups = [], $details = [], $depends = [], $values = [])
    {
        $this->groups = array_values($groups);
        $this->details = array_values($details);
        $this->depends = array_values($depends);
        $this->values = array_values($values);
    }
}