<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeAttendance
 *
 * @ORM\Table(name="org_employee_attendance")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeAttendanceRepository")
 */
class EmployeeAttendance
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
     * @var int
     *
     * @ORM\Column(name="org_employee_user_id", type="integer")
     */
    private $employeeId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="since", type="datetime")
     */
    private $since;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="till", type="datetime")
     */
    private $till;


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
     * Set employeeId.
     *
     * @param int $employeeId
     *
     * @return EmployeeAttendance
     */
    public function setEmployeeId($employeeId)
    {
        $this->employeeId = $employeeId;

        return $this;
    }

    /**
     * Get employeeId.
     *
     * @return int
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * Set since.
     *
     * @param \DateTime $since
     *
     * @return EmployeeAttendance
     */
    public function setSince($since)
    {
        $this->since = $since;

        return $this;
    }

    /**
     * Get since.
     *
     * @return \DateTime
     */
    public function getSince()
    {
        return $this->since;
    }

    /**
     * Set till.
     *
     * @param \DateTime $till
     *
     * @return EmployeeAttendance
     */
    public function setTill($till)
    {
        $this->till = $till;

        return $this;
    }

    /**
     * Get till.
     *
     * @return \DateTime
     */
    public function getTill()
    {
        return $this->till;
    }
}
