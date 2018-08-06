<?php

namespace FinanseBundle\Service;

class FinancialReserveRegisterKeys
{

    private $financialResourceId;

    public function setFinancialResourceId(int $financialResourceId)
    {
        $this->financialResourceId = $financialResourceId;
    }

    public function getFinancialResourceId()
    {
        return $this->financialResourceId;
    }

    public function __construct(int $financialResourceId)
    {
        $this->financialResourceId = $financialResourceId;
    }

}
