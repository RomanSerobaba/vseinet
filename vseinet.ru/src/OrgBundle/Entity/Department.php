<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Department
 *
 * @ORM\Table(name="org_department")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\DepartmentRepository")
 */
class Department
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
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_room_id", type="integer", nullable=true)
     */
    private $geoRoomId;

    /**
     * @var string
     *
     * @ORM\Column(name="number", type="string")
     */
    private $number;

    /**
     * @var int
     *
     * @ORM\Column(name="sort_order", type="integer")
     */
    private $sortOrder;

    /**
     * @var int
     *
     * @ORM\Column(name="pid", type="integer", nullable=true)
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(name="department_type_code", type="string")
     */
    private $typeCode;

    /**
     * @var int
     *
     * @ORM\Column(name="salary_day", type="integer", nullable=true)
     */
    private $salaryDay;

    /**
     * @var string
     *
     * @ORM\Column(name="salary_payment_type", type="string", nullable=true)
     */
    private $salaryPaymentType;

    /**
     * @var int
     *
     * @ORM\Column(name="salary_payment_source", type="integer", nullable=true)
     */
    private $salaryPaymentSource;

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
     * Set name
     *
     * @param string $name
     *
     * @return Department
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set geo room id
     *
     * @param integer $name
     *
     * @return Department
     */
    public function setGeoRoomId($geoRoomId)
    {
        $this->geoRoomId = $geoRoomId;

        return $this;
    }

    /**
     * Get geo room id
     *
     * @return integer
     */
    public function getGeoRoomId()
    {
        return $this->geoRoomId;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return Department
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set sortOrder
     *
     * @param int $sortOrder
     *
     * @return Department
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * Get sortOrder
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * Set pid
     *
     * @param integer $pid
     *
     * @return Department
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return integer
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set type code
     *
     * @param string $typeCode
     *
     * @return Department
     */
    public function setTypeCode($typeCode)
    {
        $this->typeCode = $typeCode;

        return $this;
    }

    /**
     * Get type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return $this->typeCode;
    }

    /**
     * Set salary day
     *
     * @param integer $salaryDay
     *
     * @return Department
     */
    public function setSalaryDay($salaryDay)
    {
        $this->salaryDay = $salaryDay;

        return $this;
    }

    /**
     * Get salary day
     *
     * @return integer
     */
    public function getSalaryDay()
    {
        return $this->salaryDay;
    }

    /**
     * Set salary payment type
     *
     * @param string $salaryPaymentType
     *
     * @return Department
     */
    public function setSalaryPaymentType($salaryPaymentType)
    {
        $this->salaryPaymentType = $salaryPaymentType;

        return $this;
    }

    /**
     * Get salary payment type
     *
     * @return string
     */
    public function getSalaryPaymentType()
    {
        return $this->salaryPaymentType;
    }

    /**
     * Set salary payment source
     *
     * @param integer $salaryPaymentSource
     *
     * @return Department
     */
    public function setSalaryPaymentSource($salaryPaymentSource)
    {
        $this->salaryPaymentSource = $salaryPaymentSource;

        return $this;
    }

    /**
     * Get salary payment source
     *
     * @return integer
     */
    public function getSalaryPaymentSource()
    {
        return $this->salaryPaymentSource;
    }
}
