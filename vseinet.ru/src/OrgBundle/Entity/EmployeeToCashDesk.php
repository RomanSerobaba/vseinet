<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EmployeeToCashDesk
 *
 * @ORM\Table(name="org_employee_to_cash_desk")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\EmployeeToCashDeskRepository")
 */
class EmployeeToCashDesk
{
    /**
     * @var int
     *
     * @ORM\Column(name="org_employee_user_id", type="integer")
     * @ORM\Id
     */
    private $employeeId;

    /**
     * @var int
     *
     * @ORM\Column(name="cash_desk_id", type="integer")
     * @ORM\Id
     */
    private $cashDeskId;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="is_default", type="boolean", nullable=true)
     */
    private $isDefault;


    /**
     * Set employeeId.
     *
     * @param int $employeeId
     *
     * @return EmployeeToCashDesk
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
     * Set cashDeskId.
     *
     * @param int $cashDeskId
     *
     * @return EmployeeToCashDesk
     */
    public function setCashDeskId($cashDeskId)
    {
        $this->cashDeskId = $cashDeskId;

        return $this;
    }

    /**
     * Get cashDeskId.
     *
     * @return int
     */
    public function getCashDeskId()
    {
        return $this->cashDeskId;
    }

    /**
     * Set isDefault.
     *
     * @param bool|null $isDefault
     *
     * @return EmployeeToCashDesk
     */
    public function setIsDefault($isDefault = null)
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    /**
     * Get isDefault.
     *
     * @return bool|null
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }
}
