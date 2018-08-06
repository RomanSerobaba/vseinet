<?php

namespace FinanseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - шапка
 *
 * @ORM\Table(name="bank_operation_doc_related_document")
 * @ORM\Entity()
 */

class BankOperationDocRelatedDocument
{

    ///////////////////////////
    //
    //  Поля
    //

    /**
     * @var int
     * Уникальный идентификатор документа финансовой операции
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="bank_operation_doc_did", type="integer")
     */
    private $bankOperationDocumentId;

    /**
     * @var int
     * Уникальный идентификатор связанного (рассчетного) документа
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(name="related_dcument_did", type="integer")
     */
    private $relatedDocumentId;

    /**
     * @var int
     * Уникальный идентификатор связанного (рассчетного) документа
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    // </editor-fold>

    // <editor-fold defaultstate="collapsed" desc="Методы">

    ///////////////////////////
    //
    //  Методы
    //

    // field bankOperationDocumentId

    /**
     * Получить код операции документа
     * @return string
     */
    public function getBankOperationDocumentId()
    {
        return $this->bankOperationDocumentId;
    }

    /**
     * Установить код операции документа
     * @param string $operationCode
     * @return BankOperationDocRelatedDocument
     */
    public function setBankOperationDocumentId(string $operationCode)
    {
        $this->bankOperationDocumentId = $operationCode;
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
     * @return BankOperationDocRelatedDocument
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
     * @return BankOperationDocRelatedDocument
     */
    public function setAmount(string $amount)
    {
        $this->amount = $amount;
        return $this;
    }


    // </editor-fold>

}
