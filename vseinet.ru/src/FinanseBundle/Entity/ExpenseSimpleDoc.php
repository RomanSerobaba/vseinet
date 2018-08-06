<?php

namespace FinanseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - шапка
 *
 * @ORM\Table(name="expense_simple_doc")
 * @ORM\Entity()
 */

class ExpenseSimpleDoc
{
    use \DocumentBundle\Prototipe\DocumentEntity;

    // <editor-fold defaultstate="collapsed" desc="Поля">

    ///////////////////////////
    //
    //  Поля
    //

    /**
     * Идентификатор представительства
     * @var int
     * @ORM\Column(name="org_department_id", type="integer", nullable=true)
     */
    private $orgDepartmentId;

    /**
     * Идентификатор оборудования
     * @var int
     * @ORM\Column(name="equipment_id", type="integer", nullable=true)
     */
    private $equipmentId;

    /**
     * Сумма расхода
     * @var int
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * Идентификатор статьи расхода
     * @var int
     * @ORM\Column(name="item_of_expenses_id", type="integer")
     */
    private $itemOfExpensesId;

    /**
     * Ожидаемая дата выполнения расхода
     * @var \DateTime
     * @ORM\Column(name="expected_date_execute", type="datetime")
     */
    private $expectedDateExecute;

    /**
     * Описание расхода
     * @var string
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * Идентификатор источника финансов
     * @var int
     * @ORM\Column(name="financial_resource_id", type="integer")
     */
    private $financialResourceId;

    /**
     * Время одобрения расхода
     * @var \DateTime
     * @ORM\Column(name="accepted_at", type="datetime", nullable=true)
     */
    private $acceptedAt;

    /**
     * Время запрета расхода
     * @var \DateTime
     * @ORM\Column(name="rejected_at", type="datetime", nullable=true)
     */
    private $rejectedAt;

    /**
     * Идентификатор пользователя одобрившего расход
     * @var int
     * @ORM\Column(name="accepted_by", type="integer")
     */
    private $acceptedBy;

    /**
     * Идентификатор пользователя отклонившего расход
     * @var int
     * @ORM\Column(name="rejected_by", type="integer")
     */
    private $rejectedBy;

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Методы">

    ///////////////////////////
    //
    //  Методы
    //

    /**
     *
     * @return int
     */
    public function getExpectedDateExecute()
    {
        return $this->expectedDateExecute;
    }

    /**
     *
     * @param int $expectedDateExecute
     * @return ExpenseSimpleDoc
     */
    public function setExpectedDateExecute(\DateTime $expectedDateExecute)
    {
        $this->expectedDateExecute = $expectedDateExecute;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getItemOfExpensesId()
    {
        return $this->itemOfExpensesId;
    }

    /**
     *
     * @param int $itemOfExpensesId
     * @return ExpenseSimpleDoc
     */
    public function setItemOfExpensesId(int $itemOfExpensesId)
    {
        $this->itemOfExpensesId = $itemOfExpensesId;
        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getEquipmentId()
    {
        return $this->equipmentId;
    }

    /**
     *
     * @param int|null $equipmentId
     * @return ExpenseSimpleDoc
     */
    public function setEquipmentId($equipmentId = null)
    {
        $this->equipmentId = $equipmentId;
        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getOrgDepartmentId()
    {
        return $this->orgDepartmentId;
    }

    /**
     *
     * @param int|null $orgDepartmentId
     * @return ExpenseSimpleDoc
     */
    public function setOrgDepartmentId($orgDepartmentId = null)
    {
        $this->orgDepartmentId = $orgDepartmentId;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     *
     * @param int $amount
     * @return ExpenseSimpleDoc
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     *
     * @param string $description
     * @return ExpenseSimpleDoc
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getAcceptedBy()
    {
        return $this->acceptedBy;
    }

    /**
     *
     * @param int $acceptedBy
     * @return ExpenseSimpleDoc
     */
    public function setAcceptedBy(int $acceptedBy)
    {
        $this->acceptedBy = $acceptedBy;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getRejectedBy()
    {
        return $this->rejectedBy;
    }

    /**
     *
     * @param int $rejectedBy
     * @return ExpenseSimpleDoc
     */
    public function setRejectedBy(int $rejectedBy)
    {
        $this->rejectedBy = $rejectedBy;
        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getFinancialResourceId()
    {
        return $this->financialResourceId;
    }

    /**
     *
     * @param int|null $financialResourceId
     * @return ExpenseSimpleDoc
     */
    public function setFinancialResourceId($financialResourceId = null)
    {
        $this->financialResourceId = $financialResourceId;
        return $this;
    }

    /**
     * Получить дату создания докуменнта
     * @return \DateTime|null
     */
    public function getAcceptedAt()
    {
        return $this->acceptedAt;
    }

    /**
     * Установить дату создания документа
     * @param \DateTime|null $acceptedAt
     * @return object
     */
    public function setAcceptedAt($acceptedAt = null)
    {
        $this->acceptedAt = $acceptedAt;

        return $this;
    }

    /**
     * Получить дату создания докуменнта
     * @return \DateTime|null
     */
    public function getRejectedAt()
    {
        return $this->rejectedAt;
    }

    /**
     * Установить дату создания документа
     * @param \DateTime|null $rejectedAt
     * @return object
     */
    public function setRejectedAt($rejectedAt)
    {
        $this->rejectedAt = $rejectedAt;

        return $this;
    }

    // </editor-fold>
}
