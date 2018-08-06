<?php

namespace OrgBundle\Components\Salary;

class AllOrders extends ClientsOrders
{
    /**
     * @inheritDoc
     */
    protected function constructEmployeeQuery($employeeId)
    {
        $this->from['oi'] = 'INNER JOIN OrderBundle:OrderItem AS oi WITH sr.orderItemId = oi.id';

        $this->clause[] = 'oi.createdBy IN (:employeeId)';
        $this->params['employeeId'] = $employeeId;
    }

    /**
     * @inheritDoc
     */
    protected function constructDepartmentQuery($departmentId)
    {
        $this->from['oi']   = 'INNER JOIN OrderBundle:OrderItem AS oi WITH sr.orderItemId = oi.id';
        $this->from['oetd'] = 'INNER JOIN OrgBundle:EmployeeToDepartment AS oetd WITH oi.createdBy = oetd.employeeUserId
                AND oetd.activeSince <= :till AND (oetd.activeTill IS NULL OR oetd.activeTill >= :since)';
        $this->from['odp']  = 'INNER JOIN OrgBundle:DepartmentPath AS odp WITH oetd.departmentId = odp.departmentId';

        $this->clause[] = 'odp.pid IN (:departmentId)';
        $this->params['departmentId'] = $departmentId;
    }

    /**
     * @inheritDoc
     */
    protected function constructPointQuery($pointId)
    {
        $this->from['oi'] = 'INNER JOIN OrderBundle:OrderItem AS oi WITH sr.orderItemId = oi.id';
        $this->from['od'] = 'INNER JOIN OrderBundle:OrderDoc AS od WITH oi.orderId = od.number';
        $this->from['rr'] = 'INNER JOIN OrgBundle:Representative AS rr WITH od.geoPointId = rr.geoPointId';

        $this->clause[] = 'rr.departmentId IN (:geoPointId)';
        $this->params['geoPointId'] = $pointId;
    }

    /**
     * @inheritDoc
     */
    protected function constructCityQuery($cityId)
    {
        $this->from['oi'] = 'INNER JOIN OrderBundle:OrderItem AS oi WITH sr.orderItemId = oi.id';
        $this->from['od'] = 'INNER JOIN OrderBundle:OrderDoc AS od WITH oi.orderId = od.number';

        $this->clause[] = 'od.geoCityId IN (:cityId)';
        $this->params['cityId'] = $cityId;
    }

    /**
     * @inheritDoc
     */
    protected function constructAreaQuery($areaId)
    {
        $this->from['oi']  = 'INNER JOIN OrderBundle:OrderItem AS oi WITH sr.orderItemId = oi.id';
        $this->from['od']  = 'INNER JOIN OrderBundle:OrderDoc AS od WITH oi.orderId = od.number';
        $this->from['rr']  = 'INNER JOIN OrgBundle:Representative AS rr WITH od.geoPointId = rr.geoPointId';
        $this->from['dtd'] = 'INNER JOIN OrgBundle:DepartmentToDepartment AS dtd WITH rr.departmentId = dtd.departmentId
                AND dtd.activeSince <= :till AND (dtd.activeTill IS NULL OR dtd.activeTill >= :since)';
        $this->from['dp']  = 'INNER JOIN OrgBundle:DepartmentPath AS dp WITH dtd.departmentId = dp.departmentId';

        $this->clause[] = 'dp.pid IN (:areaId)';
        $this->params['areaId'] = $areaId;
    }
}