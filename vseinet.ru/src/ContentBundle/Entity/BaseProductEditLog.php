<?php

namespace ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BaseProductEditLog
 *
 * @ORM\Table(name="base_product_edit_log")
 * @ORM\Entity(repositoryClass="ContentBundle\Repository\BaseProductEditLogRepository")
 */
class BaseProductEditLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="base_product_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $baseProductId;

    /**
     * @var int
     *
     * @ORM\Column(name="content_manager_user_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $managerId;

    /**
     * @var string
     *
     * @ORM\Column(name="target", type="string")
     */
    private $target;

    /**
     * @var int
     *
     * @ORM\Column(name="target_id", type="integer", nullable=true)
     */
    private $targetId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="changed_at", type="datetime")
     */
    private $changedAt;

    /**
     * @var string
     *
     * @ORM\Column(name="old_value", type="text")
     */
    private $oldValue;

    /**
     * @var string
     *
     * @ORM\Column(name="new_value", type="text")
     */
    private $newValue;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set baseProductId
     *
     * @param integer $baseProductId
     *
     * @return BaseProductEditLog
     */
    public function setBaseProductId($baseProductId)
    {
        $this->baseProductId = $baseProductId;

        return $this;
    }

    /**
     * Get baseProductId
     *
     * @return int
     */
    public function getBaseProductId()
    {
        return $this->baseProductId;
    }

    /**
     * Set managerId
     *
     * @param integer $managerId
     *
     * @return BaseProductEditLog
     */
    public function setManagerId($managerId)
    {
        $this->managerId = $managerId;

        return $this;
    }

    /**
     * Get managerId
     *
     * @return int
     */
    public function getManagerId()
    {
        return $this->managerId;
    }

    /**
     * Set target
     *
     * @param string $target
     *
     * @return BaseProductEditLog
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set targetId
     *
     * @param integer $targetId
     *
     * @return BaseProductEditLog
     */
    public function setTargetId($targetId)
    {
        $this->targetId = $targetId;

        return $this;
    }

    /**
     * Get targetId
     *
     * @return int
     */
    public function getTargetId()
    {
        return $this->targetId;
    }

    /**
     * Set changedAt
     *
     * @param \DateTime $changedAt
     *
     * @return BaseProductEditLog
     */
    public function setChangedAt($changedAt)
    {
        $this->changedAt = $changedAt;

        return $this;
    }

    /**
     * Get changedAt
     *
     * @return \DateTime
     */
    public function getChangedAt()
    {
        return $this->changedAt;
    }

    /**
     * Set oldValue
     *
     * @param string $oldValue
     *
     * @return BaseProductEditLog
     */
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * Get oldValue
     *
     * @return string
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * Set newValue
     *
     * @param string $newValue
     *
     * @return BaseProductEditLog
     */
    public function setNewValue($newValue)
    {
        $this->newValue = $newValue;

        return $this;
    }

    /**
     * Get newValue
     *
     * @return string
     */
    public function getNewValue()
    {
        return $this->newValue;
    }
}

