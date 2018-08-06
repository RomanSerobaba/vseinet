<?php 

namespace ContentBundle\Bus\Template\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ParserDetail
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

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $contentDetailIds;


    public function __construct($id, $name, $groupId, $isHidden, $contentDetailIds)
    {
        $this->id = $id;
        $this->name = $name;
        $this->groupId = $groupId;
        $this->isHidden = $isHidden;
        if (!empty($contentDetailIds)) {
            $this->contentDetailIds = array_map('intval', explode(',', $contentDetailIds));
        }
    }
}