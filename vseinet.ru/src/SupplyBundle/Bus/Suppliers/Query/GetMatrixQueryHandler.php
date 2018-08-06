<?php 

namespace SupplyBundle\Bus\Suppliers\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Rule;
use Doctrine\ORM\Query\ResultSetMapping;

class GetMatrixQueryHandler extends MessageHandler
{
    /**
     * @param GetMatrixQuery $query
     *
     * @return array
     */
    public function handle(GetMatrixQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $managerToSuppliers = [];
        $q = $em->createNativeQuery('
            SELECT
                id,
                manager_id
            FROM 
                supplier
            WHERE 
                manager_id IS NOT NULL
        ', new ResultSetMapping());

        $rows = $q->getResult('ListAssocHydrator');
        foreach ($rows as $row) {
            $managerToSuppliers[$row['manager_id']][$row['id']] = $row;
        }

        $supplierToCounteragent = [];
        $q = $em->createNativeQuery('
            SELECT 
                c.*,
                stc.is_main,
                stc.supplier_id
            FROM
                counteragent c 
                INNER JOIN supplier_to_counteragent stc ON stc.counteragent_id = c.id
        ', new ResultSetMapping());

        $rows = $q->getResult('ListAssocHydrator');
        foreach ($rows as $row) {
            $supplierToCounteragent[$row['supplier_id']][$row['id']] = $row;
        }

        $supplierContacts = [];
        $q = $em->createNativeQuery('
            SELECT
                c.*,
                s.id AS supplier_id
            FROM
                contact c 
                INNER JOIN person p ON p."id" = c.person_id
                INNER JOIN "user" u ON u.person_id = p.id
                INNER JOIN supplier s ON s.manager_id = u.id
            WHERE
                s.id > 0
            ORDER BY
                c.contact_type_code ASC
        ', new ResultSetMapping());

        $rows = $q->getResult('ListAssocHydrator');
        foreach ($rows as $row) {
            $supplierContacts[$row['supplier_id']][$row['id']] = $row;
        }

        ////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////

        $q = $em->createNativeQuery('
            SELECT 
                DISTINCT u.id,
                person.lastname,
                person.firstname,
                person.secondname
            FROM
                supplier s
                INNER JOIN "user" u ON u.id = s.manager_id
                INNER JOIN person ON person.id = u.person_id
                LEFT JOIN acl_user_codex c1 ON c1.user_id = u.id AND c1.acl_rule_id = ( SELECT id FROM acl_rule WHERE code = :code )
                LEFT JOIN acl_user_codex c2 ON c2.user_id = u.id AND c2.acl_rule_id = ( SELECT id FROM acl_rule WHERE code = :code ) 
            WHERE
             	( c1.is_allowed = TRUE OR c2.is_allowed = TRUE ) 
            ORDER BY
                person.lastname,
                person.firstname
        ', new ResultSetMapping());

        $q->setParameter('code', Rule::CODE_WAREHOUSE_MANAGEMENT);
        $managers = $q->getResult('ListAssocHydrator');

        foreach ($managers as $key => $val) {
            $managers[$key]['warehouses'] = isset($managerToSuppliers[$val['id']]) ? $managerToSuppliers[$val['id']] : [];
        }

        $q = $em->createNativeQuery('
            SELECT
                s.id,
                s.name,
                s.code,
                s.description,
                s.approved_at AS approvedAt,
                s.approved_by AS approvedBy,
                s.site_url AS siteUrl,
                s.auth_url AS authUrl,
                s.auth_login AS authLogin,
                s.auth_password AS authPassword,
                s.auth_comment AS authComment,
                s.contract_till AS contractTill,
                s.contract_updated_by AS contractUpdatedBy,
                s.geo_point_id AS geoPointId,
                s.order_threshold_schedule AS orderThresholdSchedule,
                s.order_delivery_schedule AS orderDeliverySchedule,
                s.order_threshold_time AS orderThresholdTime,
                s.order_delivery_date AS orderDeliveryDate,
                s.has_free_delivery AS hasFreeDelivery,
                s.manager_id AS managerId,
                s.is_active AS isActive,
                s.shipping_start AS shippingStart,
                s.previous_order_delivery_date AS previousOrderDeliveryDate
            FROM
                supplier s
            WHERE
                s.is_active = TRUE
            ORDER BY
                s.code
        ', new ResultSetMapping());

        $suppliers = $q->getResult('ListAssocHydrator');

        $q = $em->createNativeQuery('
            SELECT
                s.id,
                s.name,
                s.code,
                s.description,
                s.approved_at AS approvedAt,
                s.approved_by AS approvedBy,
                s.site_url AS siteUrl,
                s.auth_url AS authUrl,
                s.auth_login AS authLogin,
                s.auth_password AS authPassword,
                s.auth_comment AS authComment,
                s.contract_till AS contractTill,
                s.contract_updated_by AS contractUpdatedBy,
                s.geo_point_id AS geoPointId,
                s.order_threshold_schedule AS orderThresholdSchedule,
                s.order_delivery_schedule AS orderDeliverySchedule,
                s.order_threshold_time AS orderThresholdTime,
                s.order_delivery_date AS orderDeliveryDate,
                s.has_free_delivery AS hasFreeDelivery,
                s.manager_id AS managerId,
                s.is_active AS isActive,
                s.shipping_start AS shippingStart,
                s.previous_order_delivery_date AS previousOrderDeliveryDate,
                person.firstname,
                person.lastname,
                person.secondname
            FROM
                supplier s
                INNER JOIN "user" u ON u.id = s.manager_id 
                INNER JOIN person ON person.id = u.person_id
            WHERE
                s.is_active = FALSE
                AND s.approved_by IS NOT NULL
            ORDER BY
                s.code
        ', new ResultSetMapping());

        $suppliersInactive = $q->getResult('ListAssocHydrator');

        foreach ($suppliersInactive as $index => $curr) {
            $suppliersInactive[$index]['counteragents'] = isset($supplierToCounteragent[$curr['id']]) ? $supplierToCounteragent[$curr['id']] : [];
            $suppliersInactive[$index]['contacts'] = isset($supplierContacts[$curr['id']]) ? $supplierContacts[$curr['id']] : [];
        }

        return $this->camelizeKeys(['managers' => $managers, 'suppliers' => $suppliers, 'suppliersInactive' => $suppliersInactive,]);
    }
}