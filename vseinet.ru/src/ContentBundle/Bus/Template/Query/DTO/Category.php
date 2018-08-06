<?php 

namespace ContentBundle\Bus\Template\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Category
{
    /**
     * @Assert\Type(type="integer")
     */
    public $id;

    /**
     * @Assert\Type(type="integer")
     */
    public $pid;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="string")
     */
    public $basename;

    /**
     * @Assert\Choice({"male", "female", "neuter", "plural"}, strict=true)
     */
    public $gender;

    /**
     * @Assert\Choice({"none", "manual", "auto"}, strict=true)
     */
    public $tpl;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isTplEnabled;

    /**
     * @Assert\Type(type="boolea")
     */
    public $useExname;

    /**
     * @Assert\Type(type="integer")
     */
    public $aliasForId;

    /**
     * @Assert\Type(type="string")
     */
    public $linkedCategoryName;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isLeaf;


    public function __construct($id, $pid, $name, $basename, $gender, $tpl, $isTplEnabled, $useExname, $aliasForId, $linkedCategoryName, $isLeaf)
    {
        $this->id = $id;
        $this->pid = $pid;
        $this->name = $name;
        $this->basename = $basename;
        $this->gender = $gender;
        $this->tpl = $tpl;
        $this->isTplEnabled = $isTplEnabled;
        $this->useExname = $useExname;
        $this->aliasForId = $aliasForId;
        $this->linkedCategoryName = $linkedCategoryName;
        $this->isLeaf = $isLeaf;
    }
}