<?php 

namespace ContentBundle\Bus\ManagerManagment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\Choice({"none", "manual", "auto"}, strict=true)
     */
    public $tpl;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isTplEnabled;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isLeaf;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $categoryIds = [];

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $managerIds = [];


    public function __construct($id, $name, $pid, $tpl, $isTplEnabled, $categoryIds, $managerIds) 
    {
        $this->id = $id;
        $this->name = $name;
        $this->pid = $pid;
        $this->tpl = $tpl;
        $this->isTplEnabled = $isTplEnabled;
        $this->isLeaf = empty($categoryIds);
        if (!$this->isLeaf) {
            $this->categoryIds = array_map('intval', explode(',', $categoryIds));
        }
        if ($managerIds) {
            $this->managerIds = array_map('intval', explode(',', $managerIds));
        }
    }
}
