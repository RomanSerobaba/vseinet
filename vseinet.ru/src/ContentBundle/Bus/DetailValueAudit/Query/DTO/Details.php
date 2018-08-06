<?php 

namespace ContentBundle\Bus\DetailValueAudit\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Details
{    
    /**
     * @Assert\Type(type="array<integer>")
     */
    public $rootIds;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\DetailValueAudit\Query\DTO\Category>")
     */
    public $categories;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\DetailValueAudit\Query\DTO\Detail>")
     */
    public $details;


    public function __construct($rootCategories = [], $leafCategories = [], $details = [])
    {
        $this->rootIds = array_keys($rootCategories);
        $this->categories = array_values($rootCategories + $leafCategories);
        $this->details = array_values($details);
    }
}