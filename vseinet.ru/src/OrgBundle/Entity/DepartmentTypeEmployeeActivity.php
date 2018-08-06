<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DepartmentTypeEmployeeActivity
 *
 * @ORM\Table(name="org_department_type_employee_activity")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\DepartmentTypeEmployeeActivityRepository")
 */
class DepartmentTypeEmployeeActivity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="org_activity_id_seq")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="org_department_type_code", type="string", length=255)
     */
    private $departmentTypeCode;

    /**
     * @var int|null
     *
     * @ORM\Column(name="org_activity_index_id", type="integer", nullable=true)
     */
    private $activityIndexId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="org_activity_object_id", type="integer", nullable=true)
     */
    private $activityObjectId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="org_activity_area_id", type="integer", nullable=true)
     */
    private $activityAreaId;

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
     * @var bool
     *
     * @ORM\Column(name="is_chief", type="boolean")
     */
    private $isChief;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_department", type="boolean")
     */
    private $isDepartment;

    /**
     * @var int
     *
     * @ORM\Column(name="coefficient", type="integer")
     */
    private $coefficient;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_planned", type="boolean")
     */
    private $isPlanned;

    /**
     * @var int|null
     *
     * @ORM\Column(name="rate", type="integer", nullable=true)
     */
    private $rate;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

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
     * Set departmentTypeCode.
     *
     * @param string $departmentTypeCode
     *
     * @return DepartmentTypeEmployeeActivity
     */
    public function setDepartmentTypeCode($departmentTypeCode)
    {
        $this->departmentTypeCode = $departmentTypeCode;

        return $this;
    }

    /**
     * Get departmentTypeCode.
     *
     * @return string
     */
    public function getDepartmentTypeCode()
    {
        return $this->departmentTypeCode;
    }

    /**
     * Set activityIndexId.
     *
     * @param int|null $activityIndexId
     *
     * @return DepartmentTypeEmployeeActivity
     */
    public function setActivityIndexId($activityIndexId = null)
    {
        $this->activityIndexId = $activityIndexId;

        return $this;
    }

    /**
     * Get activityIndexId.
     *
     * @return int|null
     */
    public function getActivityIndexId()
    {
        return $this->activityIndexId;
    }

    /**
     * Set activityObjectId.
     *
     * @param int|null $activityObjectId
     *
     * @return DepartmentTypeEmployeeActivity
     */
    public function setActivityObjectId($activityObjectId = null)
    {
        $this->activityObjectId = $activityObjectId;

        return $this;
    }

    /**
     * Get activityObjectId.
     *
     * @return int|null
     */
    public function getActivityObjectId()
    {
        return $this->activityObjectId;
    }

    /**
     * Set activityAreaId.
     *
     * @param int|null $activityAreaId
     *
     * @return DepartmentTypeEmployeeActivity
     */
    public function setActivityAreaId($activityAreaId = null)
    {
        $this->activityAreaId = $activityAreaId;

        return $this;
    }

    /**
     * Get activityAreaId.
     *
     * @return int|null
     */
    public function getActivityAreaId()
    {
        return $this->activityAreaId;
    }

    /**
     * Set categoryId.
     *
     * @param int|null $categoryId
     *
     * @return DepartmentTypeEmployeeActivity
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
     * @return DepartmentTypeEmployeeActivity
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
     * Set isChief.
     *
     * @param bool $isChief
     *
     * @return DepartmentTypeEmployeeActivity
     */
    public function setIsChief($isChief)
    {
        $this->isChief = $isChief;

        return $this;
    }

    /**
     * Get isChief.
     *
     * @return bool
     */
    public function getIsChief()
    {
        return $this->isChief;
    }

    /**
     * Set isDepartment.
     *
     * @param bool $isDepartment
     *
     * @return DepartmentTypeEmployeeActivity
     */
    public function setIsDepartment($isDepartment)
    {
        $this->isDepartment = $isDepartment;

        return $this;
    }

    /**
     * Get isDepartment.
     *
     * @return bool
     */
    public function getIsDepartment()
    {
        return $this->isDepartment;
    }

    /**
     * Set coefficient.
     *
     * @param int $coefficient
     *
     * @return DepartmentTypeEmployeeActivity
     */
    public function setCoefficient($coefficient)
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * Get coefficient.
     *
     * @return int
     */
    public function getCoefficient()
    {
        return $this->coefficient;
    }

    /**
     * Set isPlanned.
     *
     * @param bool $isPlanned
     *
     * @return DepartmentTypeEmployeeActivity
     */
    public function setIsPlanned($isPlanned)
    {
        $this->isPlanned = $isPlanned;

        return $this;
    }

    /**
     * Get isPlanned.
     *
     * @return bool
     */
    public function getIsPlanned()
    {
        return $this->isPlanned;
    }

    /**
     * Set rate.
     *
     * @param int|null $rate
     *
     * @return DepartmentTypeEmployeeActivity
     */
    public function setRate($rate = null)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate.
     *
     * @return int|null
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return DepartmentTypeEmployeeActivity
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
     * Set activityIndex.
     *
     * @param ActivityIndex|null $activityIndex
     *
     * @return DepartmentTypeEmployeeActivity
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
     * @return DepartmentTypeEmployeeActivity
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
     * @return DepartmentTypeEmployeeActivity
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
