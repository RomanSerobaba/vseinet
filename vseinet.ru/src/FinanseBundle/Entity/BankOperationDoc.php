<?php

namespace FinanseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Документ - шапка
 *
 * @ORM\Table(name="bank_operation_doc")
 * @ORM\Entity()
 */

class BankOperationDoc
{
    use \DocumentBundle\Prototipe\DocumentEntity;
    
    // <editor-fold defaultstate="collapsed" desc="Поля">    
    
    ///////////////////////////
    //
    //  Поля
    //
    
    /**
     * @var int
     * @ORM\Column(name="financial_resource_id", type="integer")
     */
    public $financialResourceId;
            
    /**
     * @var string
     * @ORM\Column(name="banc_doc_type", type="string")
     */
    public $bancDocType;
            
    /**
     * @var int
     * @ORM\Column(name="financial_counteragent", type="integer")
     */
    public $financialCounteragent;
            
    /**
     * @var int
     * @ORM\Column(name="amount", type="integer")
     */
    public $amount;
            
    /**
     * @var int
     * @ORM\Column(name="description", type="integer")
     */
    public $description;
            
    // </editor-fold>
    
    // <editor-fold defaultstate="collapsed" desc="Методы">    
    
    ///////////////////////////
    //
    //  Методы
    //
    
    public function getFinancialResourceId() {
        return $this->financialResourceId;
    }
    
    public function setFinancialResourceId($financialResourceId) {
        $this->financialResourceId = $financialResourceId;
        return $this;
    }

    public function getBancDocType() {
        return $this->bancDocType;
    }
    
    public function setBancDocType($bancDocType) {
        $this->bancDocType = $bancDocType;
        return $this;
    }

    public function getFinancialCounteragent() {
        return $this->financialCounteragent;
    }
    
    public function setFinancialCounteragent($counteragent) {
        $this->financialCounteragent = $counteragent;
        return $this;
    }

    public function getAmount() {
        return $this->amount;
    }
    
    public function setAmount($amount) {
        $this->amount = $amount;
        return $this;
    }

    public function getDescription() {
        return $this->description;
    }
    
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    // </editor-fold>
    
}
