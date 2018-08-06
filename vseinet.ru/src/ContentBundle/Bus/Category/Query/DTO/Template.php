<?php 

namespace ContentBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Template
{
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Category\Query\DTO\CategorySection>")
     */
    public $sections;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Category\Query\DTO\DetailGroup>")
     */
    public $groups;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Category\Query\DTO\Detail>")
     */
    public $details;
    
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Category\Query\DTO\DetailDepend>")
     */
    public $depends;
    
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Category\Query\DTO\DetailValue>")
     */
    public $values;
    

    public function __construct($sections, $groups = [], $details = [], $depends = [], $values = [])
    {
        $this->sections = array_values($sections);
        $this->groups = array_values($groups);
        $this->details = array_values($details);
        $this->depends = array_values($depends);
        $this->values = array_values($values);
    }
}