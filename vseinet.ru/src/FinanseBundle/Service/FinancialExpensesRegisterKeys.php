<?php

namespace FinanseBundle\Service;

class FinancialExpensesRegisterKeys
{

    private $itemOfExpensesId;
    private $orgDepartmentId;
    private $financialCounteragentId;
    private $equipmentId;

    public function setItemOfExpensesId(int $itemOfExpensesId)
    {
        $this->itemOfExpensesId = $itemOfExpensesId;
    }

    public function setOrgDepartmentId(int $orgDepartmentId = null)
    {
        $this->orgDepartmentId = $orgDepartmentId;
    }

    public function setFinancialCounteragentId(int $financialCounteragentId = null)
    {
        $this->financialCounteragentId = $financialCounteragentId;
    }

    public function setEquipmentId(int $equipmentId = null)
    {
        $this->equipmentId = $equipmentId;
    }

    public function getItemOfExpensesId()
    {
        return $this->itemOfExpensesId;
    }

    public function getOrgDepartmentId()
    {
        return $this->orgDepartmentId;
    }

    public function getFinancialCounteragentId()
    {
        return $this->financialCounteragentId;
    }

    public function getEquipmentId()
    {
        return $this->equipmentId;
    }

    public function __construct(int $itemOfExpensesId, int $orgDepartmentId = null, int $financialCounteragentId = null, int $equipmentId = null)
    {
        $this->itemOfExpensesId = $itemOfExpensesId;
        $this->orgDepartmentId = $orgDepartmentId;
        $this->financialCounteragentId = $financialCounteragentId;
        $this->equipmentId = $equipmentId;
    }

}
