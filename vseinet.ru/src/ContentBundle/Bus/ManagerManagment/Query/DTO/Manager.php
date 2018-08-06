<?php 

namespace ContentBundle\Bus\ManagerManagment\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Manager
{
    /**
     * @Assert\Type(type="integer")
     */
    public $userId;

    /**
     * @Assert\Type(type="integer")
     */
    public $groupId;

    /**
     * @Assert\Type(type="integer")
     */
    public $departmentId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="array<integer>")
     */
    public $assignedCategoryIds = [];


    public function __construct($userId, $groupId, $departmentId, $name, $assignedCategoryIds) 
    {
        $this->userId = $userId;
        $this->groupId = $groupId ?: null;
        $this->departmentId = $departmentId ?: null;
        $this->name = $name;
        if ($assignedCategoryIds) {
            $this->assignedCategoryIds = array_map('intval', explode(',', $assignedCategoryIds));
        }
    }
}
