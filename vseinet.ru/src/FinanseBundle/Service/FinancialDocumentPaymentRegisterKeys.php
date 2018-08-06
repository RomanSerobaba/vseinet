<?php

namespace FinanseBundle\Service;

class FinancialDocumentPaymentRegisterKeys
{

    private $financialCounteragentId;
    private $settlementDocumentDId;

    public function setFinancialCounteragentId(int $financialCounteragentId)
    {
        $this->financialCounteragentId = $financialCounteragentId;
    }

    public function setSettlementDocumentDId(int $settlementDocumentDId)
    {
        $this->settlementDocumentDId = $settlementDocumentDId;
    }

    public function getFinancialCounteragentId()
    {
        return $this->financialCounteragentId;
    }

    public function getSettlementDocumentDId()
    {
        return $this->settlementDocumentDId;
    }


    public function __construct(int $financialCounteragentId, int $settlementDocumentDId)
    {
        $this->financialCounteragentId = $financialCounteragentId;
        $this->settlementDocumentDId = $settlementDocumentDId;
    }

}
