<?php

namespace OrgBundle\Components\Salary;

use AppBundle\Enum\DocumentTypeCode;

class ManagedOrders extends ClientsOrders
{
    protected function init()
    {
        parent::init();

        $this->from['si'] = 'INNER JOIN SupplyBundle:SupplyItem AS si WITH sr.supplyItemId = si.id';

        $this->clause[] = 'si.parentDocType = :supply';
        $this->params['supply'] = DocumentTypeCode::SUPPLY;
        // TODO: Добавить раскомплектованые товары
    }

    /**
     * @inheritDoc
     */
    protected function constructEmployeeQuery($employeeId)
    {
        $this->from['supply']   = 'INNER JOIN SupplyBundle:Supply AS supply WITH si.parentDocId = supply.id';
        $this->from['supplier'] = 'INNER JOIN SupplyBundle:Supplier AS supplier WITH supply.supplier_id = supplier.id';

        $this->clause[] = 'supplier.managerId IN (:employeeId)';
        $this->params['employeeId'] = $employeeId;
    }

    /**
     * @inheritDoc
     */
    protected function constructDepartmentQuery($departmentId) {}
}