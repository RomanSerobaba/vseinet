<?php

namespace FinanseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - шапка
 *
 * @ORM\Table(name="financial_operation_doc_related_document")
 * @ORM\Entity()
 */

class FinancialOperationDocRelatedDocument
{

    ///////////////////////////
    //
    //  Поля
    //

    /**
     * Уникальный идентификатор документа финансовой операции
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="financial_operation_doc_did", type="integer")
     */
    private $financialOperationDocumentId;

    /**
     * Уникальный идентификатор связанного (рассчетного) документа
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="related_dcument_did", type="integer")
     */
    private $relatedDocumentId;

    /**
     * Уникальный идентификатор связанного (рассчетного) документа
     * @var int
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Методы">

    ///////////////////////////
    //
    //  Методы
    //

    // field financialOperationDocumentId

    /**
     * Получить код операции документа
     * @return string
     */
    public function getFinancialOperationDocumentId()
    {
        return $this->financialOperationDocumentId;
    }

    /**
     * Установить код операции документа
     * @param string $operationCode
     * @return FinancialOperationDocRelatedDocument
     */
    public function setFinancialOperationDocumentId(string $operationCode)
    {
        $this->financialOperationDocumentId = $operationCode;
        return $this;
    }

    // field relatedDocumentId

    /**
     * Получить идентификатор источника финансов
     * @return int
     */
    public function getRelatedDocumentId()
    {
        return $this->relatedDocumentId;
    }

    /**
     * Установить идентификатор источника финансов
     * @param int $relatedDocumentId
     * @return FinancialOperationDocRelatedDocument
     */
    public function setRelatedDocumentId(string $relatedDocumentId)
    {
        $this->relatedDocumentId = $relatedDocumentId;
        return $this;
    }

    // field amount

    /**
     * Получить сумму, зачтенную к платежу
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Установить сумму, зачтенную к платежу
     * @param int $amount
     * @return FinancialOperationDocRelatedDocument
     */
    public function setAmount(string $amount)
    {
        $this->amount = $amount;
        return $this;
    }


    // </editor-fold>

}
