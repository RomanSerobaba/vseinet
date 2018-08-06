<?php

namespace ClaimsBundle\Component;

use ClaimsBundle\Bus\GoodsIssue\Query\GetGoodsIssuesQuery;
use ClaimsBundle\Entity\GoodsIssueType;
use Doctrine\ORM\EntityManager;

class GoodsIssueComponent
{
    const SOURCE_RECLAMATION = 'reclamation';
    const RECIPIENT_EXTERNAL = 'external';

    /**
     * Entity Manager
     *
     * @var EntityManager
     */
    private $_em;

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em) : void
    {
        $this->_em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEm() : EntityManager
    {
        return $this->_em;
    }

    public function __construct(EntityManager $em)
    {
        $this->_em = $em;
    }

    /**
     * @param GetGoodsIssuesQuery $query
     *
     * @return array
     */
    public function getByFilter(GetGoodsIssuesQuery $query) : array
    {
        $criteria = $params = [];

        if (!empty($query->id)) {
            $criteria[] = "d.id IN (:defect_id)";
            $params['defect_id'] = explode(',', $query->id);
        } elseif (!empty($query->pid)) {
            $criteria[] = "(d.base_product_id = :product_id OR op.base_product_id = :product_id)";
            $params['pid'] = $query->pid;
        } else {
            if (!empty($query->ownerType)) {
                if ($query->ownerType == 1) {
                    $criteria[] = "d.order_item_id AND (d.supplier_decided_at IS NULL OR d.supplier_decided_at IS NOT NULL AND d.goods_decided_at IS NOT NULL)";
                }
                if ($query->ownerType == 2) {
                    $criteria[] = "(!d.order_item_id OR d.supplier_decided_at IS NOT NULL AND (d.supplier_decided_at IS NULL OR d.goods_decided_at IS NULL))";
                }
            }

            if (!empty($query->reclamationType)) {
                $criteria[] = "d.goods_issue_type_code IN (:types)";
                $params['types'] = $query->reclamationType;
            }

            if (!empty($query->warehouse)) {
                $criteria[] = "d.supplier_id IN (:warehouse)";
                $params['warehouse'] = $query->warehouse;
            }

            if (!empty($query->inService)) {
//                if ($query->inService == 1)
//                    $criteria[] = "!IFNULL(d.in_service,0)";
//                elseif ($query->inService == 2) {
//                    $criteria[] = "d.in_service <> 0";
//
//                    if (!empty($query->serviceId)) {
//                        $criteria[] = "d.in_service IN (:service_id:)";
//                        $params['service_id'] = $filter['service_id'];
//                    }
//                }
            }

            $params['point_id'] = !is_array($query->pointId) ? [$query->pointId] : $query->pointId;

            if (!empty($query->pointId)) {
                $criteria[] = "w.id IN (:point_id)";
            }

            if (!empty($query->isGetting)) {
//                if ($query->isGetting==1)
//                    $criteria[] = "d.is_getting IS NULL";
//                elseif ($query->isGetting==2) {
//                    if (\Arr::get($filter, 'is_getting_since')) {
//                        $criteria[] = "d.is_getting >= :is_getting_since:";
//                        $params['is_getting_since'] = $filter['is_getting_since'];
//                    }
//                    if (\Arr::get($filter, 'is_getting_till')) {
//                        $criteria[] = "d.is_getting <= :is_getting_till:";
//                        $params['is_getting_till'] = $filter['is_getting_till'];
//                    }
//                }
            }

            if (!empty($query->dateSince)) {
                $criteria[] = "IF(@a:=DATE_FORMAT(IFNULL(d.supplier_solved,'1985-01-01'),'%Y-%m-%d')>@b:=DATE_FORMAT(IFNULL(d.product_solved,'1985-01-01'),'%Y-%m-%d'),IF(@a>@c:=DATE_FORMAT(IFNULL(d.client_solved,'1985-01-01'),'%Y-%m-%d'),@a,@c),IF(@b>@c:=DATE_FORMAT(IFNULL(d.client_solved,'1985-01-01'),'%Y-%m-%d'),@b,@c)) >= :date_since:";
                $params['date_since'] = date('Y-m-d', strtotime($query->dateSince));
            }

            if (!empty($query->dateTill)) {
                $criteria[] = "IF(@a:=DATE_FORMAT(IFNULL(d.supplier_solved,'1985-01-01'),'%Y-%m-%d')>@b:=DATE_FORMAT(IFNULL(d.product_solved,'1985-01-01'),'%Y-%m-%d'),IF(@a>@c:=DATE_FORMAT(IFNULL(d.client_solved,'1985-01-01'),'%Y-%m-%d'),@a,@c),IF(@b>@c:=DATE_FORMAT(IFNULL(d.client_solved,'1985-01-01'),'%Y-%m-%d'),@b,@c)) <= :date_till:";
                $params['date_till'] = date('Y-m-d', strtotime($query->dateTill));
            }

            $criteria[] = "(d.supplier_decided_at IS NULL OR d.goods_decided_at IS NULL OR d.client_decided_at IS NULL AND d.order_item_id > 0)";
        }

        $where = empty($criteria) ? '' : ' AND '.implode(' AND ', $criteria);


        $q = $this->getEm()->createQuery('
            SELECT
                    d.*,
                    bp.name,
                    COALESCE(o.id,0) order_id,
                    COALESCE(op.retail_price,p.price) price,
                    CASE WHEN d.order_item_id > 0
                        THEN op.initial_retail_price
                        ELSE COALESCE(si.purchase_price, bp.supplier_price)
                    END AS initial_purchase_price,
                    CASE WHEN d.order_item_id IS NOT NULL AND d.order_item_id > 0
                        THEN op.retail_price
                        ELSE COALESCE(si.purchase_price, bp.supplier_price) 
                    END AS purchase_price,
                    CASE WHEN d.order_item_id IS NOT NULL AND d.order_item_id > 0
                        THEN op.transport_charges
                        ELSE NULL
                    END AS transport_charges,
                    s2.code supplier_code,
                    s.code original_supplier_code,
                    s.id original_supplier_id,
                    CASE WHEN sp.code IS NOT NULL
                        THEN sp.code
                        ELSE NULL
                    END AS supplier_product_code,
            -- 		string_agg(DISTINCT CONCAT(c.name,\' (\',gp.name,\':\'), \',\') points,
            -- 		string_agg(DISTINCT r.name, \', \') rooms,
                \'\' AS ipoints,
                    \'\' AS irooms,
                    r.id room_id,
                    u.fullname AS clientname,
                    CASE WHEN u.email IS NOT NULL
                        THEN u.email
                        ELSE NULL
                    END AS clientemail,
                    u.phone,
                    COALESCE(op.quantity,d.quantity) quantity,
                    \'\' AS tracker,
                    bp.id base_product_id,
                    \'\' AS additional_phones,
                    DATE_PART(\'day\', NOW() - d.created_at) AS "time",
                    o.created_at order_date,
                    p.id product_id,
                    dp.arriving_time,
                    dp.id departure_id,
                    up.fullname AS user_product,
                    uc.fullname AS user_client,
                    us.fullname AS user_supplier,
                    ur.fullname AS user_reclamation,
                    rp.base_product_id regrade_code,
                    \'\' destination_room_name,
                    \'\' AS destination_full_name,
                    o.payment_type_code,
                    dt.name "type",
                    s2.manager_id warehouse_manager,
            -- 		IF(((d.supplier_solved IS NOT NULL OR !d.supposed_compensation) AND d.product_solved IS NOT NULL AND (d.client_solved IS NOT NULL OR !d.order_item_id)),
            -- 				IF(@a:=DATE_FORMAT(COALESCE(d.supplier_solved,\'1985-01-01\'),\'%Y-%m-%d\')>@b:=DATE_FORMAT(COALESCE(d.product_solved,\'1985-01-01\'),\'%Y-%m-%d\'),
            -- 				IF(@a>@c:=DATE_FORMAT(COALESCE(d.client_solved,\'1985-01-01\'),\'%Y-%m-%d\'),@a,@c),
            -- 				IF(@b>@c:=DATE_FORMAT(COALESCE(d.client_solved,\'1985-01-01\'),\'%Y-%m-%d\'),@b,@c)
            -- 		),NULL) closing_date,
            -- 		DATEDIFF(COALESCE(IF(((d.supplier_solved IS NOT NULL OR !d.supposed_compensation) AND d.product_solved IS NOT NULL AND (d.client_solved IS NOT NULL OR !d.order_item_id)),
            -- 				IF(@a:=DATE_FORMAT(COALESCE(d.supplier_solved,\'1985-01-01\'),\'%Y-%m-%d\')>@b:=DATE_FORMAT(COALESCE(d.product_solved,\'1985-01-01\'),\'%Y-%m-%d\'),
            -- 				IF(@a>@c:=DATE_FORMAT(COALESCE(d.client_solved,\'1985-01-01\'),\'%Y-%m-%d\'),@a,@c),
            -- 				IF(@b>@c:=DATE_FORMAT(COALESCE(d.client_solved,\'1985-01-01\'),\'%Y-%m-%d\'),@b,@c)
            -- 		),NULL),NOW()),d.date_creation) days_passed,
                    0 torg_departure_id,
                    d.client_requirement AS repair_type,
            -- 		string_agg(DISTINCT CAST(d2.id AS text), \',\') otherDefects,
                    \'\' AS service_center,
                    o.our_seller_counteragent_id seller_id
            FROM 
                goods_issue d
                INNER JOIN goods_issue_type dt ON dt.code=d.goods_issue_type_code
                LEFT JOIN order_item op ON op.base_product_id = d.base_product_id
                LEFT JOIN "order" o ON op.order_id=o.id
                INNER JOIN product p ON p.base_product_id=COALESCE(op.base_product_id,d.base_product_id)
                LEFT JOIN goods_issue_to_supply_item rt ON d.id = rt.goods_issue_id
                LEFT JOIN supply_item si ON si.id = rt.supply_item_id
                LEFT JOIN shipment_item dtp ON dtp.id=op.id AND dtp.type = \'reserve\'	
                LEFT JOIN shipment dp ON dp.id=dtp.shipment_id AND dp.type=\'supplier\'
                LEFT JOIN base_product bp ON bp.id=p.base_product_id
                LEFT JOIN product rp ON rp.id=d.product_resort_id
                LEFT JOIN supplier s ON s.id=bp.supplier_id
                LEFT JOIN supplier_product sp ON sp.base_product_id=bp.id AND sp.supplier_id=s.id AND sp.code IS NOT NULL
                LEFT JOIN supplier_product sp2 ON sp2.base_product_id=bp.id AND sp2.supplier_id=s.id AND sp2.code IS NOT NULL AND sp.created_at<sp2.created_at
                LEFT JOIN func_view_user_person(COALESCE(o.created_by, 0)) u ON u.id=COALESCE(o.created_by, 0)
                LEFT JOIN func_view_user_person(d.client_decided_by) uc ON uc.id=d.client_decided_by
                LEFT JOIN func_view_user_person(d.goods_decided_by) up ON up.id=d.goods_decided_by
                LEFT JOIN func_view_user_person(d.supplier_decided_by) us ON us.id=d.supplier_decided_by
                LEFT JOIN func_view_user_person(d.created_by) ur ON ur.id=d.created_by	
                
                LEFT JOIN goods_release_item gri ON gri.order_item_id = op.id
                LEFT JOIN goods_release_doc gr ON gr.id = gri.goods_release_id
                LEFT JOIN geo_room r ON gr.geo_room_id = r.id
                LEFT JOIN representative w ON w.geo_point_id=r.geo_point_id
                LEFT JOIN geo_point gp ON gp.id=w.geo_point_id
                LEFT JOIN geo_city c ON c.id=gp.geo_city_id
                    
            -- 	LEFT JOIN geo_room ir ON d.initial_room_reserves LIKE CONCAT(\'%\"\',ir.id,\'\":%\')
            -- 	LEFT JOIN representative iw ON iw.id=ir.point_id
            -- 	LEFT JOIN city ic ON ic.id=iw.city_id
                
            -- 	LEFT JOIN geo_room r1 ON r1.id=d.destination_room_id
            -- 	LEFT JOIN representative p1 ON p1.id=r1.point_id
            -- 	LEFT JOIN city c1 ON c1.id=p1.city_id	
                        
                LEFT JOIN supplier s2 ON s2.id=d.supplier_id
                LEFT JOIN goods_issue d2 ON d.order_item_id IS NOT NULL AND d.order_item_id > 0 AND d2.order_item_id=d.order_item_id AND d2.id != d.id
            
                
            WHERE sp2.id IS NULL -- '.$where.'
            -- GROUP BY d.id
        ');
        $q->setParameters($params);

        $rows = $q->getArrayResult();
        foreach ($rows as $i => $row) {
            $q = $this->getEm()->createQuery("
                SELECT
                    gic.*,
                    CONCAT_WS(' ',u.lastname,u.firstname,u.secondname) AS manager
                FROM 
                    goods_issue_comment gic
                    LEFT JOIN func_view_user_person(gic.created_by) u ON u.id = gic.created_by
                WHERE gic.goods_issue_id = :id
                ORDER BY gic.created_at            
            ");
            $q->setParameter('id', $row['id']);

            $rows[$i]['comments'] = $q->getArrayResult();
        }

        return $rows;
    }

    /**
     * @param               $date
     * @param               $endDate
     *
     * @return string
     */
    private function _buildMonthlyBalanceSql($date, $endDate)
    {
        $date = $date ? : date('Y-m-01');
        $endDate = $endDate ? : strtotime('last day of this month', strtotime($date));
        $query = $this->getEm()->createQuery("
            SELECT
                COALESCE(
                    CASE WHEN d.goods_issue_type_code = 'regrading' AND (COALESCE(newbp.supplier_price, 0) - bp.supplier_price) > 0
                        THEN (COALESCE(newbp.supplier_price, 0) - bp.supplier_price)
                        ELSE 
                            CASE WHEN d.goods_decision = 'Сняли с баланса'
                                THEN -1
                                ELSE 
                                    (CASE WHEN d.goods_issue_type_code = 'found' AND d.goods_decision = 'Вернули на баланс'
                                        THEN 1
                                        ELSE 0
                                    END) *
                                    (CASE WHEN d.order_item_id IS NOT NULL
                                        THEN op.retail_price
                                        ELSE COALESCE(si.purchase_price, bp.supplier_price)
                                    END
                                    )
                            END
                    END,
                    0 
                ) * d.quantity amount,
                CASE WHEN d.supplier_decided_at IS NOT NULL
                    THEN d.supplier_compensation
                    ELSE 0
                END AS compensation,
                
                dty.name AS \"type\", 
                bp.name product, 
                d.created_at, 
                d.goods_decided_at date_solvation, 
                d.id, 
                d.quantity
            FROM
                goods_issue d
                LEFT JOIN order_item op ON op.id = d.order_item_id
                INNER JOIN product p ON p.base_product_id = op.base_product_id
                
                INNER JOIN base_product bp ON bp.id = p.base_product_id
                LEFT JOIN base_product newbp ON d.product_resort_id = newbp.id
                    
                LEFT JOIN goods_issue_to_supply_item rt ON d.id = rt.goods_issue_id
                LEFT JOIN supply_item si ON si.id = rt.supply_item_id
                INNER JOIN goods_issue_type dty ON dty.code = d.goods_issue_type_code 
            WHERE
                d.goods_decided_at BETWEEN :date AND :endDate 
                AND (
                    d.goods_issue_type_code= 'regrading' 
                    AND (COALESCE(newbp.supplier_price, 0) - bp.supplier_price) > 0
                    OR d.goods_decision = 'Сняли с баланса' 
                    OR d.goods_issue_type_code = 'found' 
                    AND d.goods_decision = 'Вернули на баланс' 
                ) 
            ORDER BY
                date_solvation
            ");
        $query->setParameter('date', $date);
        $query->setParameter('endDate', $endDate);

        return $query->getSql();
    }

    /**
     * @param null          $date
     * @param null          $endDate
     *
     * @return int
     */
    public function getMonthlyBalance($date = null, $endDate = null) : int
    {
        $q = $this->getEm()->createQuery("
                SELECT SUM(a.amount+a.compensation)
                FROM (".$this->_buildMonthlyBalanceSql($date, $endDate).") a
            ");

        return $q->getSingleScalarResult();
    }

    /**
     * @param null $vars
     *
     * @return array
     */
    public function buildDefectStatistics($vars = null) : array
    {
        $params = [];
        extract($vars);
        $select = "
            SELECT
                SUM( CASE WHEN d.orderItemId IS NOT NULL THEN p.price * op.quantity ELSE p.price END * d.quantity ) AS value
                " . (isset($select) && $select ? ", " . $select : "");
        $from = "
            FROM ClaimsBundle:GoodsIssue AS d
            LEFT JOIN OrderBundle:OrderItem AS op WITH op.id = d.orderItemId
            LEFT JOIN OrderBundle:OrderTable AS o WITH o.id = op.orderId
            INNER JOIN PricingBundle:Product AS p WITH p.baseProductId =  COALESCE(op.baseProductId, d.baseProductId)
            " . (isset($from) && $from ? $from : "");
        $clause = "
            WHERE
                d.createdAt >= :since AND d.createdAt <= :till
                AND ( d.goodsDecidedAt > :till
                    OR d.goodsDecidedAt IS NULL
                    OR d.supplierCompensation IS NOT NULL
                    AND ( d.supplierDecidedAt > :till OR d.supplierDecidedAt IS NULL )
                    OR d.orderItemId IS NOT NULL
                    AND ( d.clientDecidedAt > :till OR d.clientDecidedAt IS NULL )
                )
            " . (isset($clause) && $clause ? " AND " . $clause : "");

        if (!isset($params['since'])) {
            $params['since'] = date('Y-m-d 00:00:00', strtotime('first day of this month'));
        }

        if (!isset($params['till'])) {
            $params['till'] = date('Y-m-d 23:59:59', strtotime('last day of this month'));
        }

        $group = (isset($group) && $group ? " GROUP BY " . $group : "");
        $query = $this->getEm()->createQuery($select . $from . $clause . $group);
        $query->setParameters($params);

        return $query->getArrayResult();
    }

    /**
     * @return array
     */
    public function getGoodIssuesTypes() : array
    {
        $types = [];

        $rows = $this->getEm()->getRepository(GoodsIssueType::class)->findAll();
        foreach ($rows as $row) {
            $types[$row->getCode()] = $row->getName();
        }

        return $types;
    }

    /**
     * @return array
     */
    public function getRooms() : array
    {
        $q = $this->getEm()->createQuery("
            SELECT
                r.id AS room_id,
                r.name AS room_name,
                r.code AS room_code,
                p.name AS point_name,
                p.code AS point_code,
                p.id AS point_id,
                c.name AS city_name,
                c.id AS city_id
            FROM
                geo_room r
                INNER JOIN geo_point p ON p.id = r.geo_point_id
                INNER JOIN representative rr ON rr.geo_point_id = r.geo_point_id
                INNER JOIN geo_city c ON c.id = p.geo_city_id 
            WHERE
                rr.is_active = TRUE 
            GROUP BY
                r.id, p.id, c.id
            ORDER BY
                c.id, p.id        
        ");

        return $q->getArrayResult();
    }

    /**
     * @return array
     */
    public function getSuppliers() : array
    {
        $list = [];
        $q = $this->getEm()->createQuery("
            SELECT
                id,
                code
            FROM 
                supplier
            WHERE 
                is_active = TRUE
            ORDER BY
                code   
        ");

        $rows = $q->getArrayResult();
        foreach ($rows as $row) {
            $list[$row['id']] = $row['code'];
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getServiceCenters() : array
    {
        $list = [];
        $q = $this->getEm()->createQuery("
            SELECT
                service_center.id,
                service_center.name,
                geo_point.name AS geo_point_name,
                geo_point.code AS geo_point_code,
                supplier.id AS supplier_id,
                supplier.\"name\" AS supplier_name
            FROM 
                service_center
                INNER JOIN geo_point ON geo_point.id = service_center.geo_point_id
                INNER JOIN supplier ON supplier.id = service_center.supplier_id
            WHERE 
                service_center.is_active = TRUE
            ORDER BY
                service_center.name 
        ");

        $rows = $q->getArrayResult();
        foreach ($rows as $row) {
            $list[$row['id']] = $row;
        }

        return $list;
    }
}