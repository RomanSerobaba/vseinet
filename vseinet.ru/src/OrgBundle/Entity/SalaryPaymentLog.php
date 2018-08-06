<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SalaryPaymentLog
 *
 * @ORM\Table(name="org_salary_payment_log")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\SalaryPaymentLogRepository")
 */
class SalaryPaymentLog
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
     * @ORM\Column(name="employee_id", type="integer")
     */
    private $employeeId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var int|null
     *
     * @ORM\Column(name="expense_id", type="integer", nullable=true)
     */
    private $expenseId;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;


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
     * @return SalaryPaymentLog
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
     * Set amount.
     *
     * @param int|null $amount
     *
     * @return SalaryPaymentLog
     */
    public function setAmount($amount = null)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount.
     *
     * @return int|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set expenseId.
     *
     * @param int|null $expenseId
     *
     * @return SalaryPaymentLog
     */
    public function setExpenseId($expenseId = null)
    {
        $this->expenseId = $expenseId;

        return $this;
    }

    /**
     * Get expenseId.
     *
     * @return int|null
     */
    public function getExpenseId()
    {
        return $this->expenseId;
    }

    /**
     * Set date.
     *
     * @param \DateTime|null $date
     *
     * @return SalaryPaymentLog
     */
    public function setDate($date = null)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime|null
     */
    public function getDate()
    {
        return $this->date;
    }
}
