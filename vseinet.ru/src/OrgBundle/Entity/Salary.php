<?php

namespace OrgBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Salary
 *
 * @ORM\Table(name="org_salary")
 * @ORM\Entity(repositoryClass="OrgBundle\Repository\SalaryRepository")
 */
class Salary
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var int|null
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tax", type="integer", nullable=true)
     */
    private $tax;

    /**
     * @var int|null
     *
     * @ORM\Column(name="paid", type="integer", nullable=true)
     */
    private $paid;

    /**
     * @var int|null
     *
     * @ORM\Column(name="fines", type="integer", nullable=true)
     */
    private $fines;

    /**
     * @var float|null
     *
     * @ORM\Column(name="coefficient", type="float", nullable=true)
     */
    private $coefficient;

    /**
     * @var int|null
     *
     * @ORM\Column(name="queue_amount", type="integer", nullable=true)
     */
    private $queueAmount;


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
     * @return Salary
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
     * Set date.
     *
     * @param \DateTime $date
     *
     * @return Salary
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date.
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set amount.
     *
     * @param int|null $amount
     *
     * @return Salary
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
     * Set tax.
     *
     * @param int|null $tax
     *
     * @return Salary
     */
    public function setTax($tax = null)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax.
     *
     * @return int|null
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set paid.
     *
     * @param int|null $paid
     *
     * @return Salary
     */
    public function setPaid($paid = null)
    {
        $this->paid = $paid;

        return $this;
    }

    /**
     * Get paid.
     *
     * @return int|null
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * Set fines.
     *
     * @param int|null $fines
     *
     * @return Salary
     */
    public function setFines($fines = null)
    {
        $this->fines = $fines;

        return $this;
    }

    /**
     * Get fines.
     *
     * @return int|null
     */
    public function getFines()
    {
        return $this->fines;
    }

    /**
     * Set coefficient.
     *
     * @param float|null $coefficient
     *
     * @return Salary
     */
    public function setCoefficient($coefficient = null)
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    /**
     * Get coefficient.
     *
     * @return float|null
     */
    public function getCoefficient()
    {
        return $this->coefficient;
    }

    /**
     * Set queueAmount.
     *
     * @param int|null $queueAmount
     *
     * @return Salary
     */
    public function setQueueAmount($queueAmount = null)
    {
        $this->queueAmount = $queueAmount;

        return $this;
    }

    /**
     * Get queueAmount.
     *
     * @return int|null
     */
    public function getQueueAmount()
    {
        return $this->queueAmount;
    }
}
