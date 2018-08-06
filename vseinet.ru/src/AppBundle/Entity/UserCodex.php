<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCodex
 *
 * @ORM\Table(name="acl_user_codex")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserCodexRepository")
 */
class UserCodex
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $userId;

    /**
     * @var int
     *
     * @ORM\Column(name="acl_rule_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $ruleId;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_allowed", type="boolean")
     */
    private $isAllowed;


    /**
     * Set userId
     *
     * @param integer $userId
     *
     * @return UserCodex
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set ruleId
     *
     * @param integer $ruleId
     *
     * @return UserCodex
     */
    public function setRuleId($ruleId)
    {
        $this->ruleId = $ruleId;

        return $this;
    }

    /**
     * Get ruleId
     *
     * @return int
     */
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * Set isAllowed
     *
     * @param boolean $isAllowed
     *
     * @return UserCodex
     */
    public function setIsAllowed($isAllowed)
    {
        $this->isAllowed = $isAllowed;

        return $this;
    }

    /**
     * Get isAllowed
     *
     * @return bool
     */
    public function getIsAllowed()
    {
        return $this->isAllowed;
    }
}
