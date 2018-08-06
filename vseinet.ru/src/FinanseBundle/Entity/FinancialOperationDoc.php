<?php

namespace FinanseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - шапка
 *
 * @ORM\Table(name="financial_operation_doc")
 * @ORM\Entity()
 */
class FinancialOperationDoc
{

    use \DocumentBundle\Prototipe\DocumentEntity;

    // <editor-fold defaultstate="collapsed" desc="Поля">
    ///////////////////////////
    //
    //  Поля

    //

    /**
     * @var string
     * @ORM\Column(name="operation_code", type="string")
     */
    private $operationCode;

    /**
     * @var int
     * Идентификатор источника финансов
     * @ORM\Column(name="financial_resource_id", type="integer")
     */
    private $financialResourceId;

    /**
     * @var int
     * Сумма платежа
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    // </editor-fold>
    // <editor-fold defaultstate="collapsed" desc="Методы">
    ///////////////////////////
    //
    //  Методы
    //

    // field operationCode

    /**
     * Получить код операции документа
     * @return string
     */
    public function getOperationCode()
    {
        return $this->operationCode;
    }

    /**
     * Установить код операции документа
     * @param string $operationCode
     * @return FinancialOperationDoc
     */
    public function setOperationCode(string $operationCode)
    {
        $this->operationCode = $operationCode;
        return $this;
    }

    // field financialResourceId

    /**
     * Получить идентификатор источника финансов
     * @return int|null
     */
    public function getFinancialResourceId()
    {
        return $this->financialResourceId;
    }

    /**
     * Установить идентификатор источника финансов
     * @param int|null $financialResourceId
     * @return FinancialOperationDoc
     */
    public function setFinancialResourceId($financialResourceId = null)
    {
        $this->financialResourceId = $financialResourceId;
        return $this;
    }

    // field amount

    /**
     * Получить идентификатор источника финансов
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Установить идентификатор источника финансов
     * @param int $amount
     * @return FinancialOperationDoc
     */
    public function setAmount(int $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    // </editor-fold>
}
