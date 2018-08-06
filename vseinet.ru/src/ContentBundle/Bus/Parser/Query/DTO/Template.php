<?php 

namespace ContentBundle\Bus\Parser\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Template
{
    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Parser\Query\DTO\Source>")
     */
    public $sources;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Parser\Query\DTO\DetailGroup>")
     */
    public $groups;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Parser\Query\DTO\Detail>")
     */
    public $details;
    

    public function __construct($sources = [], $groups = [], $details = [])
    {
        $this->sources = array_values($sources);
        $this->groups = array_values($groups);
        $this->details = array_values($details);
    }
}