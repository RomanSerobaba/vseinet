<?php

namespace FinanseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - шапка
 *
 * @ORM\Table(name="accountable_expenses_doc")
 * @ORM\Entity()
 */
class AccountableExpensesDoc
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
     * Идентификатор подотчетного лица
     * @var int
     * @ORM\Column(name="financial_counteragent_id", type="integer")
     */
    private $financialCounteragentId;

    /**
     * Сумма затребованная/выданная под отчет
     * @var int
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * Идентификатор статьи расхода, на которую будут списаны выданные средства
     * @var int
     * @ORM\Column(name="to_item_of_expenses_id", type="integer")
     */
    private $toItemOfExpensesId;

    /**
     * Идентификатор оборудования, на которое будут списаны выданные средства
     * @var int
     * @ORM\Column(name="to_equipment_id", type="integer", nullable=true)
     */
    private $toEquipmentId;

    /**
     * Ожидаемая дата выдачи средств (выполнения расхода)
     * @var \DateTime
     * @ORM\Column(name="expected_date_execute", type="datetime")
     */
    private $expectedDateExecute;

    /**
     * Ожидаемая дата отчета по выданным средствам
     * @var \DateTime
     * @ORM\Column(name="maturity_date_execute", type="datetime", nullable=true)
     */
    private $maturityDateExecute;

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
     * @var \DateTime
     * @ORM\Column(name="accepted_at", type="datetime", nullable=true)
     */
    private $acceptedAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="rejected_at", type="datetime", nullable=true)
     */
    private $rejectedAt;

    /**
     * Идентификатор пользователя одобрившего расход
     * @var int
     * @ORM\Column(name="accepted_by", type="integer", nullable=true)
     */
    private $acceptedBy;

    /**
     * Идентификатор пользователя отклонившего расход
     * @var int
     * @ORM\Column(name="rejected_by", type="integer", nullable=true)
     */
    private $rejectedBy;

    /**
     * @var \DateTime
     * @ORM\Column(name="payment_at", type="datetime", nullable=true)
     */
    private $paymentAt;

    /**
     * Идентификатор пользователя одобрившего расход
     * @var int
     * @ORM\Column(name="payment_by", type="integer", nullable=true)
     */
    private $paymentBy;

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
     * @return AccountableExpensesDoc
     */
    public function setExpectedDateExecute(\DateTime $expectedDateExecute)
    {
        $this->expectedDateExecute = $expectedDateExecute;
        return $this;
    }

    /**
     *
     * @return \DateTime|null
     */
    public function getMaturityDatePayment()
    {
        return $this->maturityDateExecute;
    }

    /**
     *
     * @param \DateTime|null $maturityDateExecute
     * @return AccountableExpensesDoc
     */
    public function setMaturityDatePayment(\DateTime $maturityDateExecute = null)
    {
        $this->maturityDateExecute = $maturityDateExecute;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getToItemOfExpensesId()
    {
        return $this->toItemOfExpensesId;
    }

    /**
     *
     * @param int $toItemOfExpensesId
     * @return AccountableExpensesDoc
     */
    public function setToItemOfExpensesId(int $toItemOfExpensesId)
    {
        $this->toItemOfExpensesId = $toItemOfExpensesId;
        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getToEquipmentId()
    {
        return $this->toEquipmentId;
    }

    /**
     *
     * @param int|null $toEquipmentId
     * @return AccountableExpensesDoc
     */
    public function setToEquipmentId(int $toEquipmentId = null)
    {
        $this->toEquipmentId = $toEquipmentId;
        return $this;
    }

    /**
     *
     * @return int
     */
    public function getFinancialCounteragentId()
    {
        return $this->financialCounteragentId;
    }

    /**
     *
     * @param int $financialCounteragentId
     * @return AccountableExpensesDoc
     */
    public function setFinancialCounteragentId($financialCounteragentId)
    {
        $this->financialCounteragentId = $financialCounteragentId;
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
     * @return AccountableExpensesDoc
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
     * @return AccountableExpensesDoc
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
     * @return AccountableExpensesDoc
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getAcceptedBy()
    {
        return $this->acceptedBy;
    }

    /**
     *
     * @param int|null $acceptedBy
     * @return AccountableExpensesDoc
     */
    public function setAcceptedBy(int $acceptedBy = null)
    {
        $this->acceptedBy = $acceptedBy;
        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getRejectedBy()
    {
        return $this->rejectedBy;
    }

    /**
     *
     * @param int|null $rejectedBy
     * @return AccountableExpensesDoc
     */
    public function setRejectedBy(int $rejectedBy = null)
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
     * @return AccountableExpensesDoc
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
    public function setRejectedAt(\DateTime $rejectedAt = null)
    {
        $this->rejectedAt = $rejectedAt;

        return $this;
    }

    /**
     * Получить дату выдачи денег
     * @return \DateTime|null
     */
    public function getPaymentAt()
    {
        return $this->paymentAt;
    }

    /**
     * Установить дату выдачи денег
     * @param \DateTime|null $paymentAt
     * @return object
     */
    public function setPaymentAt(\DateTime $paymentAt = null)
    {
        $this->paymentAt = $paymentAt;

        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getPaymentBy()
    {
        return $this->paymentBy;
    }

    /**
     *
     * @param int|null $paymentBy
     * @return AccountableExpensesDoc
     */
    public function setPaymentBy(int $paymentBy = null)
    {
        $this->paymentBy = $paymentBy;
        return $this;
    }

    // </editor-fold>
}
