<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubroleCodex
 *
 * @ORM\Table(name="acl_subrole_codex")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SubroleCodexRepository")
 */
class SubroleCodex
{
    /**
     * @var int
     *
     * @ORM\Column(name="acl_subrole_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $subroleId;

    /**
     * @var int
     *
     * @ORM\Column(name="acl_rule_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $ruleId;


    /**
     * Set subroleId
     *
     * @param integer $subroleId
     *
     * @return SubroleCodex
     */
    public function setSubroleId($subroleId)
    {
        $this->subroleId = $subroleId;

        return $this;
    }

    /**
     * Get subroleId
     *
     * @return int
     */
    public function getSubroleId()
    {
        return $this->subroleId;
    }

    /**
     * Set ruleId
     *
     * @param integer $ruleId
     *
     * @return SubroleCodex
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
}
