<?php

namespace OrgBundle\Components\Salary;

use OrgBundle\Entity\ActivityIndex;

class ClientsOrders extends Base\AbstractComponent
{
    protected function init()
    {
        parent::init();
        $this->from['sr'] = 'OrderBundle:SalesRegister AS sr';
    }

    /**
     * @inheritDoc
     */
    protected function constructIndexQuery(ActivityIndex $activityIndex)
    {
        switch ($activityIndex->getCode()) {
            case 'quantity':
                $this->select[] = 'SUM(sr.delta) AS ' . $activityIndex->getCode();

                break;
            case 'selling':
                $this->select[] = 'SUM(coi.retailPrice * sr.delta) AS ' . $activityIndex->getCode();

                $this->from['coi'] = 'INNER JOIN OrderBundle:ClientOrderItem AS coi WITH sr.orderItemId = coi.orderItemId';

                break;
            case 'margin':
                $this->select[] = 'SUM((coi.retailPrice - si.purchasePrice) * sr.delta) AS ' . $activityIndex->getCode();

                $this->from['coi'] = 'INNER JOIN OrderBundle:ClientOrderItem AS coi WITH sr.orderItemId = coi.orderItemId';
                $this->from['si']  = 'INNER JOIN SupplyBundle:SupplyItem AS si WITH sr.supplyItemId = si.id';

                break;
            case 'supply':
            default:
                $this->select[] = 'SUM(si.purchasePrice * sr.delta) AS ' . $activityIndex->getCode();

                $this->from['si']  = 'INNER JOIN SupplyBundle:SupplyItem AS si WITH sr.supplyItemId = si.id';
                break;
        }
    }

    /**
     * @inheritDoc
     */
    protected function constructDateQuery($since, $till)
    {
//        $this->select[] = "DATE_FORMAT(sr.registeredAt, '%Y-%m-01') month";
        if ($since) {
            $this->clause[] = 'sr.registeredAt >= :since';
            $this->params['since'] = $since;
        }
        if ($till) {
            $this->clause[] = 'sr.registeredAt <= :till';
            $this->params['till'] = $till;
        }
//        $this->group[] = "month";
//        $this->order[] = "month";
    }

    /**
     * @inheritDoc
     */
    protected function constructEmployeeQuery($employeeId)
    {
        $this->from['oi'] = 'INNER JOIN OrderBundle:OrderItem AS oi WITH sr.orderItemId = oi.id';
        $this->from['od'] = 'INNER JOIN OrderBundle:OrderDoc AS od WITH oi.orderId = od.number';

        $this->clause[] = 'od.managerId IN (:employeeId)';
        $this->params['employeeId'] = $employeeId;
    }

    /**
     * @inheritDoc
     */
    protected function constructDepartmentQuery($departmentId)
    {
        $this->from['oi']   = 'INNER JOIN OrderBundle:OrderItem AS oi WITH sr.orderItemId = oi.id';
        $this->from['od']   = 'INNER JOIN OrderBundle:OrderDoc AS od WITH oi.orderId = od.number';
        $this->from['oetd'] = 'INNER JOIN OrgBundle:EmployeeToDepartment AS oetd WITH od.managerId = oetd.employeeUserId
                AND oetd.activeSince <= :till AND (oetd.activeTill IS NULL OR oetd.activeTill >= :since)';
        $this->from['odp']  = 'INNER JOIN OrgBundle:DepartmentPath AS odp WITH oetd.departmentId = odp.departmentId';

        $this->clause[] = 'odp.pid IN (:departmentId)';
        $this->params['departmentId'] = $departmentId;
    }

    /**
     * @inheritDoc
     */
    protected function constructCategoryQuery($categoryId)
    {
        $this->from['oi'] = 'INNER JOIN OrderBundle:OrderItem AS oi WITH sr.orderItemId = oi.id';
        $this->from['bp'] = 'INNER JOIN ContentBundle:BaseProduct AS bp WITH oi.baseProductId = bp.id';
        $this->from['cp'] = 'INNER JOIN ContentBundle:CategoryPath AS cp WITH bp.categoryId = cp.id';

        $this->clause[] = 'cp.pid IN (:categoryId)';
        $this->params['categoryId'] = $categoryId;
    }
}