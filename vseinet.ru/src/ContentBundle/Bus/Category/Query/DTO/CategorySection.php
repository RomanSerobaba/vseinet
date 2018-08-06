<?php 

namespace ContentBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CategorySection
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
    public $categoryId;

    /**
     * @Assert\Type(type="string")
     */
    public $basename;
    
    /**
     * @Assert\Choice({"male", "female", "neuter", "plural"}, strict=true)
     */
    public $gender;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $detailIdsByGroupIds;


    public function __construct($id, $name, $categoryId, $basename, $gender)
    {
        $this->id = $id;
        $this->name = $name;
        $this->categoryId = $categoryId;
        $this->basename = $basename;
        $this->gender = $gender;
    }
}