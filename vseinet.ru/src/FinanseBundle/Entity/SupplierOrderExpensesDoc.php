<?php

namespace FinanseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - шапка
 *
 * @ORM\Table(name="supplier_order_expenses_doc")
 * @ORM\Entity()
 */

class SupplierOrderExpensesDoc
{
    use \DocumentBundle\Prototipe\DocumentEntity;


    // <editor-fold defaultstate="collapsed" desc="Поля">

    ///////////////////////////
    //
    //  Поля
    //

    /**
     * @var int
     * Идентификатор представительства
     * @ORM\Column(name="org_вepartment_шd", type="integer", nullable=true)
     */
    private $orgDepartmentId;

    /**
     * @var int
     * Идентификатор подотчетного лица
     * @ORM\Column(name="financial_counteragent_id", type="integer", nullable=true)
     */
    private $financialCounteragentId;

    /**
     * @var int
     * Сумма оплаты бонусами
     * @ORM\Column(name="amount_bonus", type="integer", nullable=true)
     */
    private $amountBonus;

    /**
     * @var int
     * Сумма оплаты взаиморасчетом
     * @ORM\Column(name="amount_mutual", type="integer", nullable=true)
     */
    private $amountMutual;

    /**
     * @var int
     * Сумма оплаты из финансового источника
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var int
     * Идентификатор статьи расхода
     * @ORM\Column(name="item_of_expenses_id", type="integer")
     */
    private $itemOfExpensesId;

    /**
     * @var \DateTime
     * Ожидаемая дата выполнения расхода
     * @ORM\Column(name="expected_date_execute", type="datetime")
     */
    private $expectedDateExecute;

    /**
     * @var string
     * Описание расхода
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var int
     * Идентификатор источника финансов
     * @ORM\Column(name="financial_resource_id", type="integer", nullable=true)
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
     * @var int
     * Идентификатор пользователя одобрившего расход
     * @ORM\Column(name="accepted_by", type="integer")
     */
    private $acceptedBy;

    /**
     * @var int
     * Идентификатор пользователя отклонившего расход
     * @ORM\Column(name="rejected_by", type="integer")
     */
    private $rejectedBy;

    /**
     * @var array
     * @ORM\Column(name="relative_documents_ids", type="json")
     */
    private $relativeDocumentsIds;

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Методы">
    /////////////////////////////
    //
    //  Методы
    //

    /**
     *
     * @return int
     */
    public function getExpectedDateExecute()
    {
        $this->eeee = 1;
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
     * @return int
     */
    public function getItemOfExpensesId()
    {
        return $this->itemOfExpensesId;
    }

    /**
     *
     * @param int $itemOfExpensesId
     * @return AccountableExpensesDoc
     */
    public function setItemOfExpensesId(int $itemOfExpensesId)
    {
        $this->itemOfExpensesId = $itemOfExpensesId;
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
    public function setFinancialCounteragentId(int $financialCounteragentId)
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
    public function getAmountBonus()
    {
        return $this->amountBonus;
    }

    /**
     *
     * @param int|null $amountBonus
     * @return AccountableExpensesDoc
     */
    public function setAmountBonus(int $amountBonus = null)
    {
        $this->amountBonus = $amountBonus;
        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getAmountMutual()
    {
        return $this->amountMutual;
    }

    /**
     *
     * @param int|null $amountMutual
     * @return AccountableExpensesDoc
     */
    public function setAmountMutual(int $amountMutual = null)
    {
        $this->amountMutual = $amountMutual;
        return $this;
    }

    /**
     *
     * @return int|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     *
     * @param int|null $amount
     * @return AccountableExpensesDoc
     */
    public function setAmount(int $amount = null)
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
     * @return int
     */
    public function getAcceptedBy()
    {
        return $this->acceptedBy;
    }

    /**
     *
     * @param int $acceptedBy
     * @return AccountableExpensesDoc
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
     * @return AccountableExpensesDoc
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
     * @return AccountableExpensesDoc
     */
    public function setFinancialResourceId($financialResourceId = null)
    {
        $this->financialResourceId = $financialResourceId;
        return $this;
    }

    /**
     * Get relativeDocumentsIds
     * @return array
     */
    public function getRelativeDocumentsIds()
    {
        return $this->relativeDocumentsIds;
    }

    /**
     * Set relativeDocumentsIds
     * @param array|null $relativeDocumentsIds
     * @return GoodsAcceptance
     */
    public function setRelativeDocumentsIds($relativeDocumentsIds = null)
    {
        $this->relativeDocumentsIds = $relativeDocumentsIds;

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
