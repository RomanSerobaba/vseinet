<?php

namespace OrgBundle\Components\Salary;

use OrgBundle\Entity\ActivityIndex;

class Reclamations extends Base\AbstractComponent
{
    protected function init()
    {
        parent::init();
        $this->from['d'] = 'ReservesBundle:GoodsIssueRegister AS d';
    }

    /**
     * @inheritdoc
     */
    protected function constructIndexQuery(ActivityIndex $activityIndex)
    {
        switch ($activityIndex->getCode()) {
            case 'quantity':
                $this->select[] = 'SUM(
                    CASE
                        WHEN d.deltaGoods > d.deltaClient THEN
                            CASE
                                WHEN d.deltaGoods > d.deltaSupplier THEN
                                    d.deltaGoods
                                ELSE
                                    d.deltaSupplier
                            END
                        WHEN d.deltaClient > d.deltaSupplier THEN
                            d.deltaClient
                        ELSE
                            d.deltaSupplier
                    END) AS ' . $activityIndex->getCode();
                break;

            case 'supply':
            default:
                $this->select[] = 'SUM((
                    CASE
                        WHEN d.deltaGoods > d.deltaClient THEN
                            CASE
                                WHEN d.deltaGoods > d.deltaSupplier THEN
                                    d.deltaGoods
                                ELSE
                                    d.deltaSupplier
                            END
                        WHEN d.deltaClient > d.deltaSupplier THEN
                            d.deltaClient
                        ELSE
                            d.deltaSupplier
                    END) * si.purchasePrice) AS ' . $activityIndex->getCode();

                $this->from['gid'] = 'INNER JOIN ReservesBundle:GoodsIssueDoc AS gid WITH d.goodsIssueDocDId = gid.dId';
                $this->from['si']  = 'INNER JOIN SupplyBundle:SupplyItem AS si WITH gid.supplyItemId = si.id';
                break;
        }
    }

    /**
     * @inheritdoc
     */
    protected function constructDateQuery($since, $till)
    {
//        $this->select[] = "DATE_FORMAT(d.createdAt, '%Y-%m-01') month";
        if ($since) {
            $this->clause[] = 'd.createdAt >= :since';
            $this->params['since'] = $since;
        }
        if ($till) {
            $this->clause[] = 'd.createdAt <= :till';
            $this->params['till'] = $till;
        }
//        $this->group[] = "month";
//        $this->order[] = "month";
    }

    /**
     * @inheritDoc
     */
    protected function constructPointQuery($pointId)
    {
        $this->from['gid'] = 'INNER JOIN ReservesBundle:GoodsIssueDoc AS gid WITH d.goodsIssueDocDId = gid.dId';
        $this->from['gr']  = 'INNER JOIN OrgBundle:GeoRoom AS gr WITH gid.geoRoomId = gr.id';
        $this->from['rr']  = 'INNER JOIN OrgBundle:Representative AS rr WITH gr.geoPointId = rr.geoPointId';

        $this->clause[] = 'rr.departmentId IN (:pointId)';
        $this->params['pointId'] = $pointId;
    }

    /**
     * @inheritDoc
     */
    protected function constructCityQuery($cityId)
    {
        $this->from['gid'] = 'INNER JOIN ReservesBundle:GoodsIssueDoc AS gid WITH d.goodsIssueDocDId = gid.dId';
        $this->from['gr']  = 'INNER JOIN OrgBundle:GeoRoom AS gr WITH gid.geoRoomId = gr.id';
        $this->from['gp']  = 'INNER JOIN OrgBundle:GeoPoint AS gp WITH gr.geoPointId = gp.id';

        $this->clause[] = 'gp.geoCityId IN (:cityId)';
        $this->params['cityId'] = $cityId;
    }

    /**
     * @inheritDoc
     */
    protected function constructAreaQuery($areaId)
    {
        $this->from['gid'] = 'INNER JOIN ReservesBundle:GoodsIssueDoc AS gid WITH d.goodsIssueDocDId = gid.dId';
        $this->from['gr']  = 'INNER JOIN OrgBundle:GeoRoom AS gr WITH gid.geoRoomId = gr.id';
        $this->from['rr']  = 'INNER JOIN OrgBundle:Representative AS rr WITH gr.geoPointId = rr.geoPointId';
        $this->from['odp'] = 'INNER JOIN OrgBundle:DepartmentPath AS odp WITH rr.departmentId = odp.departmentId';

        $this->clause[] = 'odp.pid IN (:departmentId)';
        $this->params['departmentId'] = $areaId;
    }

    /**
     * @inheritDoc
     */
    protected function constructCategoryQuery($categoryId)
    {
        $this->from['gid'] = 'INNER JOIN ReservesBundle:GoodsIssueDoc AS gid WITH d.goodsIssueDocDId = gid.dId';
        $this->from['bp']  = 'INNER JOIN ContentBundle:BaseProduct AS bp WITH gid.baseProductId = bp.id';
        $this->from['cp']  = 'INNER JOIN ContentBundle:CategoryPath AS cp WITH bp.categoryId = cp.id';

        $this->clause[] = 'cp.pid IN (:categoryId)';
        $this->params['categoryId'] = $categoryId;
    }
}