<?php 

namespace ContentBundle\Bus\Category\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Annotation as VIA;

class Category
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
    public $pid;
    
    /**
     * @Assert\Type(type="integer")
     */
    public $aliasForId;

    /**
     * @Assert\Type(type="string")
     */
    public $linkedCategoryName;

    /**
     * @Assert\Type(type="string")
     */
    private $basename;

    /**
     * @Assert\Type(type="boolean")
     */
    private $useExname;

    /**
     * @Assert\Choice({"male", "female", "neuter", "plural"}, strict=true)
     */
    private $gender;

    /**
     * @Assert\Type(type="string")
     */
    private $tpl;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isLeaf;

    /**
     * @Assert\Type(type="array<ContentBundle\Bus\Category\Query\DTO\Breadcrumb>")
     */
    public $breadcrumbs;

    /**
     * @Assert\Type(type="ContentBundle\Entity\CategorySeo")
     */
    public $seo;


    public function __construct($id, $name, $pid, $aliasForId, $linkedCategoryName, $basename, $useExname, $gender, $tpl, $isLeaf)
    {
        $this->id = $id;
        $this->name = $name;
        $this->pid = $pid;
        $this->aliasForId = $aliasForId;
        $this->linkedCategoryName = $linkedCategoryName;
        $this->basename = $basename;
        $this->useExname = $useExname;
        $this->gender = $gender;
        $this->tpl = $tpl;
        $this->isLeaf = $isLeaf;
    }
}