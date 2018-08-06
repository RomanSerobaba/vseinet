<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table(name="org_activity")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\ActivityRepository")
 */
class Activity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="org_activity_index_id", type="integer")
     */
    private $activityIndexId;

    /**
     * @var int
     *
     * @ORM\Column(name="org_activity_object_id", type="integer")
     */
    private $activityObjectId;

    /**
     * @var int
     *
     * @ORM\Column(name="org_activity_area_id", type="integer")
     */
    private $activityAreaId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="org_activity_area_value", type="integer", nullable=true)
     */
    private $activityAreaValue;

    /**
     * @var int|null
     *
     * @ORM\Column(name="category_id", type="integer", nullable=true)
     */
    private $categoryId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="interval_month", type="integer", nullable=true)
     */
    private $intervalMonth;

    /**
     * @var int|null
     *
     * @ORM\Column(name="org_department_type_activity_id", type="integer", nullable=true)
     */
    private $departmentTypeActivityId;

    /**
     * @var ActivityIndex
     *
     * @ORM\ManyToOne(targetEntity="OrgBundle\Entity\ActivityIndex")
     * @ORM\JoinColumn(name="org_activity_index_id", referencedColumnName="id")
     */
    private $activityIndex;

    /**
     * @var ActivityObject
     *
     * @ORM\ManyToOne(targetEntity="OrgBundle\Entity\ActivityObject")
     * @ORM\JoinColumn(name="org_activity_object_id", referencedColumnName="id")
     */
    private $activityObject;

    /**
     * @var ActivityArea
     *
     * @ORM\ManyToOne(targetEntity="OrgBundle\Entity\ActivityArea")
     * @ORM\JoinColumn(name="org_activity_area_id", referencedColumnName="id")
     */
    private $activityArea;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Activity
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set activityIndexId.
     *
     * @param int $activityIndexId
     *
     * @return Activity
     */
    public function setActivityIndexId($activityIndexId)
    {
        $this->activityIndexId = $activityIndexId;

        return $this;
    }

    /**
     * Get activityIndexId.
     *
     * @return int
     */
    public function getActivityIndexId()
    {
        return $this->activityIndexId;
    }

    /**
     * Set activityObjectId.
     *
     * @param int $activityObjectId
     *
     * @return Activity
     */
    public function setActivityObjectId($activityObjectId)
    {
        $this->activityObjectId = $activityObjectId;

        return $this;
    }

    /**
     * Get activityObjectId.
     *
     * @return int
     */
    public function getActivityObjectId()
    {
        return $this->activityObjectId;
    }

    /**
     * Set activityAreaId.
     *
     * @param int $activityAreaId
     *
     * @return Activity
     */
    public function setActivityAreaId($activityAreaId)
    {
        $this->activityAreaId = $activityAreaId;

        return $this;
    }

    /**
     * Get activityAreaId.
     *
     * @return int
     */
    public function getActivityAreaId()
    {
        return $this->activityAreaId;
    }

    /**
     * Set activityAreaValue.
     *
     * @param int|null $activityAreaValue
     *
     * @return Activity
     */
    public function setActivityAreaValue($activityAreaValue = null)
    {
        $this->activityAreaValue = $activityAreaValue;

        return $this;
    }

    /**
     * Get activityAreaValue.
     *
     * @return int|null
     */
    public function getActivityAreaValue()
    {
        return $this->activityAreaValue;
    }

    /**
     * Set categoryId.
     *
     * @param int|null $categoryId
     *
     * @return Activity
     */
    public function setCategoryId($categoryId = null)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId.
     *
     * @return int|null
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set intervalMonth.
     *
     * @param int|null $intervalMonth
     *
     * @return Activity
     */
    public function setIntervalMonth($intervalMonth = null)
    {
        $this->intervalMonth = $intervalMonth;

        return $this;
    }

    /**
     * Get intervalMonth.
     *
     * @return int|null
     */
    public function getIntervalMonth()
    {
        return $this->intervalMonth;
    }

    /**
     * Set departmentTypeActivityId.
     *
     * @param int|null $departmentTypeActivityId
     *
     * @return Activity
     */
    public function setDepartmentTypeActivityId($departmentTypeActivityId = null)
    {
        $this->departmentTypeActivityId = $departmentTypeActivityId;

        return $this;
    }

    /**
     * Get departmentTypeActivityId.
     *
     * @return int|null
     */
    public function getDepartmentTypeActivityId()
    {
        return $this->departmentTypeActivityId;
    }

    /**
     * Set activityIndex.
     *
     * @param ActivityIndex|null $activityIndex
     *
     * @return Activity
     */
    public function setActivityIndex($activityIndex = null)
    {
        $this->setActivityIndexId($activityIndex->getId());
        $this->activityIndex = $activityIndex;

        return $this;
    }

    /**
     * Get activityIndex.
     *
     * @return ActivityIndex|null
     */
    public function getActivityIndex()
    {
        return $this->activityIndex;
    }

    /**
     * Set activityObject.
     *
     * @param ActivityObject|null $activityObject
     *
     * @return Activity
     */
    public function setActivityObject($activityObject = null)
    {
        $this->setActivityObjectId($activityObject->getId());
        $this->activityObject = $activityObject;

        return $this;
    }

    /**
     * Get activityObject.
     *
     * @return ActivityObject|null
     */
    public function getActivityObject()
    {
        return $this->activityObject;
    }

    /**
     * Set activityArea.
     *
     * @param ActivityArea|null $activityArea
     *
     * @return Activity
     */
    public function setActivityArea($activityArea = null)
    {
        $this->setActivityAreaId($activityArea->getId());
        $this->activityArea = $activityArea;

        return $this;
    }

    /**
     * Get activityArea.
     *
     * @return ActivityArea|null
     */
    public function getActivityArea()
    {
        return $this->activityArea;
    }
}
