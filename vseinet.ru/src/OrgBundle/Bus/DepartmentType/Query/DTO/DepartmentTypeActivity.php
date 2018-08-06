<?php

namespace OrgBundle\Bus\DepartmentType\Query\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DepartmentTypeActivity
{
    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     */
    public $departmentTypeId;

    /**
     * @Assert\Type(type="string")
     */
    public $departmentTypeCode;

    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     */
    public $activityId;

    /**
     * @Assert\Type(type="string")
     */
    public $name;

    /**
     * @Assert\Type(type="integer")
     */
    public $activityIndexId;

    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     */
    public $activityObjectId;

    /**
     * @Assert\Type(type="integer")
     */
    public $activityAreaId;

    /**
     * @Assert\Type(type="integer")
     */
    public $categoryId;

    /**
     * @Assert\Type(type="string")
     */
    public $categoryName;

    /**
     * @Assert\Type(type="integer")
     */
    public $intervalMonth;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isChief;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isDepartment;

    /**
     * @Assert\Type(type="integer")
     * @Assert\NotBlank
     */
    public $coefficient;

    /**
     * @Assert\Type(type="boolean")
     */
    public $isPlanned;

    /**
     * @Assert\Type(type="integer")
     */
    public $rate;

    /**
     * DepartmentTypeActivity constructor.
     * @param $departmentTypeId
     * @param $departmentTypeCode
     * @param $activityId
     * @param $name
     * @param $activityIndexId
     * @param $activityObjectId
     * @param $activityAreaId
     * @param $categoryId
     * @param $categoryName
     * @param $intervalMonth
     * @param $isChief
     * @param $isDepartment
     * @param $coefficient
     * @param $isPlanned
     * @param $rate
     */
    public function __construct(
        $departmentTypeId,
        $departmentTypeCode,
        $activityId,
        $name,
        $activityIndexId,
        $activityObjectId,
        $activityAreaId,
        $categoryId,
        $categoryName,
        $intervalMonth,
        $isChief,
        $isDepartment,
        $coefficient,
        $isPlanned,
        $rate
    )
    {
        $this->departmentTypeId = $departmentTypeId;
        $this->departmentTypeCode = $departmentTypeCode;
        $this->activityId = $activityId;
        $this->name = $name;
        $this->activityIndexId = $activityIndexId;
        $this->activityObjectId = $activityObjectId;
        $this->activityAreaId = $activityAreaId;
        $this->categoryId = $categoryId;
        $this->categoryName = $categoryName;
        $this->intervalMonth = $intervalMonth;
        $this->isChief = $isChief;
        $this->isDepartment = $isDepartment;
        $this->coefficient = $coefficient;
        $this->isPlanned = $isPlanned;
        $this->rate = $rate;
    }
}