<?php

namespace AccountingBundle\Bus\Clients\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\ContactType;
use AppBundle\Entity\Role;
use OrderBundle\Entity\OrderItemStatus;
use Doctrine\ORM\Query\ResultSetMapping;


class GetClientsQueryHandler extends MessageHandler
{
    /**
     * @param GetClientsQuery $query
     *
     * @return array
     */
    public function handle(GetClientsQuery $query): array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if ($query->type === 'org') {
            $whereExpression = "WHERE c.name IS NOT NULL ";
            $havingExpressionAllOrdersCount = ' HAVING COUNT(DISTINCT o.id) > 0 ';

            if (!empty($query->title)) {
                $search = trim($query->title);
                $whereExpression .= ' AND c.name LIKE "%'.$search.'%" ';
            }

            if (!empty($query->tin)) {
                $search = trim($query->tin);
                $whereExpression .= ' AND c.tin='.$search.' ';
            }

            if (!empty($query->cityId)) {
                $whereExpression .= ' AND o.geo_city_id = "' . intval($query->cityId) . '"';
            }

            if ($query->sortBy === 'last_order_date') {
                $sortBy = " o.created_at DESC ";
            } elseif ($query->sortBy === 'orders_count') {
                $sortBy = " ordersCount DESC ";
            } elseif ($query->sortBy === 'orders_sum') {
                $sortBy = " ordersSum DESC ";
            } elseif ($query->sortBy === 'orders_profit') {
                $sortBy = " ordersProfit DESC ";
            } else {
                $sortBy = "
                TRIM(
                REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(LOWER(c.name), 
                LOWER('ИП '), '') , 
                LOWER('ООО '), '') ,
                LOWER('ЗАО '), '') , 
                LOWER('АНО '), '') ,
                LOWER('МДОУ '), '') , 
                LOWER('ГБУЗ '), '') ,
                LOWER('ГБУ '), '') , 
                LOWER('ГУ '), '') ,
                LOWER('Акционерное общество '), '') , 
                LOWER('Закрытое акционерное общество '), '') ,
                LOWER('Открытое акционерное общество '), '') , 
                LOWER('Общество с ограниченной ответственностью '), '') ,
                LOWER('ФГБУ '), '') , 
                LOWER('Индивидуальный предприниматель '), '') ,
                LOWER('ОАО '), '') , 
                LOWER('»'), '') ,
                LOWER('«'), '') , 
                LOWER('\"'), '') ,
                LOWER('\" '), '') ,
                LOWER('“'), '') ,
                LOWER('”'), '') ,
                LOWER('АО '), '') ,
                LOWER('ooo '), ''),
                LOWER('Государственное бюджетное учреждение '), ''),
                LOWER('Государственное автономное учреждение '), ''),
                LOWER('Государственное учреждение -  '), ''))
                ASC ";
            }

            $q = $em->createNativeQuery("
                SELECT 
                    COUNT(DISTINCT o.id) ordersCount, 
                    o.created_at lastOrderDate, 
                    c.id id, 
                    c.\"name\", 
                    c.tin tin, 
                    SUM(CASE WHEN op.order_item_status_code IS NOT NULL THEN op.quantity * CASE WHEN op.franchiser_client_price > 0 THEN op.franchiser_client_price ELSE op.retail_price END + COALESCE((
                        SELECT SUM(COALESCE(pctp.price, 0)) 
                        FROM service pctp 
                        INNER JOIN service_type pct ON pct.id=pctp.service_type_id 
                        INNER JOIN service_to_order_item stoi ON stoi.service_id=pctp.id
                        WHERE stoi.order_item_id=op.id),0) ELSE 0 END ) ordersSum, 
                 
                    SUM(CASE WHEN op.order_item_status_code IS NOT NULL THEN op.quantity * CASE WHEN op.franchiser_client_price > 0 THEN op.franchiser_client_price ELSE op.retail_price END - COALESCE(op.quantity * op.transport_charges,0) - COALESCE(op.quantity * so.price,0) + COALESCE(pctp1.price,0) ELSE 0 END) ordersProfit, 
                    o.managerId, 
                    CONCAT(vup.firstname, ': ', vup.phone, ', ', vup.mobile) AS phone,
                    vup.email
                FROM 
                    counteragent c
                    LEFT JOIN \"order\" o ON o.client_counteragent_id=c.id
                    LEFT JOIN counteragent c1 ON c1.id=o.our_seller_counteragent_id
                    LEFT JOIN order_item op ON op.order_id=o.id AND op.order_item_status_code IN ('created','shipping','callable','transit','arrived','courier','transport', 'post', 'releasable', 'completed', 'issued')
                    
                    LEFT JOIN service_to_order_item stoi ON stoi.order_item_id=op.id
                    LEFT JOIN service pctp1 ON pctp1.id=stoi.service_id
                    LEFT JOIN service_type pct1 ON pct1.id=pctp1.service_type_id
                    
                    LEFT JOIN supplier_order so ON op.base_product_id = so.base_product_id 
                    LEFT JOIN func_view_user_person(o.user_id) vup ON vup.id = o.user_id 
                
               ".$whereExpression." 
                GROUP BY c.id, o.id
                ".$havingExpressionAllOrdersCount."
                ORDER BY " . $sortBy, new ResultSetMapping());

            return $q->getResult('ListHydrator');
        } else {
            $whereExpression = '';

            if ($query->sortBy == 'role') {
                $sortBy = 'ar.sort_order, p.lastname, p.firstname';
            } else {
                $sortBy = 'p.lastname, p.firstname';
            }

            if (!empty($query->lfs)) {
                $search = trim(preg_replace('~\s~is','.*',preg_replace('~[^a-zA-Zа-яА-Я]+~isu', ' ', $query->lfs)));
                $whereExpression .= ' AND (LOWER(CONCAT(COALESCE(p.lastname,"")," ",p.firstname," ",COALESCE(p.secondname,""))) SIMILAR TO LOWER("'.$search.'") OR LOWER(CONCAT(p.firstname," ",p.secondname," ",p.lastname)) SIMILAR TO LOWER("'.$search.'"))';
            }
            if (!empty($query->phone)) {
                $search = preg_replace('~(^8|^\+*7|[^\d]+)~isu', '', trim($query->phone));
                $whereExpression .= sprintf(" AND cnt.value SIMILAR TO '%s' AND cnt.contact_type_code IN('%s', '%s')", $search, ContactType::CODE_PHONE, ContactType::CODE_MOBILE);
            }
            if (!empty($query->email)) {
                $search = trim($query->email);
                $whereExpression .= sprintf(" AND cnt.value SIMILAR TO '%s' AND cnt.contact_type_code = '%s'", $search, ContactType::CODE_EMAIL);
            }
            switch($query->type) {
                case 'cl':
                    $whereExpression .= sprintf(" AND ar.code = '%s'", Role::CODE_CLIENT);
                    break;
                case 'wh':
                    $whereExpression .= sprintf(" AND ar.code IN ('%s', '%s')", Role::CODE_WHOLESALER, Role::CODE_FRANCHISER);
                    break;
            }

            $q = $em->createNativeQuery("
                SELECT
                    u.id,
                    u.is_marketing_subscribed AS isMarketingSubscribed,
                    u.is_transactional_subscribed AS isTransactionalSubscribed,
                    p.id AS personId,
                    p.firstname,
                    p.secondname,
                    p.lastname,
                    p.birthday,
                    p.gender,
                    c.name city,
                    ar.code roleCode,
                    ar.summary roleName,
                    ar.sort_order rolePosition,
                    string_agg(CONCAT(cnt.contact_type_code, '|', cnt.value), ',') as contacts
                ".($query->type === 'wh' ? ',SUM ( op.quantity ) has_orders' : '')."
                FROM
                    \"user\" u
                    INNER JOIN person AS p ON p.id = u.person_id
                    LEFT JOIN contact cnt ON cnt.person_id = p.id
                    INNER JOIN user_to_acl_subrole AS utasr ON utasr.user_id = u.id
                    INNER JOIN acl_subrole AS asr ON asr.id = utasr.acl_subrole_id
                    INNER JOIN acl_role AS ar ON ar.id = asr.acl_role_id	
                    LEFT OUTER JOIN geo_city c ON c.id = u.geo_city_id
                    LEFT OUTER JOIN org_employee ed ON ed.user_id = u.id
                ".($query->type === 'wh' ? '
                    LEFT OUTER JOIN "order" o ON o.user_id = u.id
                    LEFT OUTER JOIN order_item op ON o.id = op.order_id AND op.order_item_status_code = :arrived
                ' : '') . "
                WHERE
                    LENGTH(COALESCE(u.password, '')) > 0 
                    AND ar.code != 'ADMIN' 
                    AND ed.user_id IS NULL
                    ".$whereExpression."
                GROUP BY
                    u.id, p.id, c.id, ar.id
                ORDER BY " . $sortBy . " LIMIT 100 OFFSET 0", new ResultSetMapping());

            $q->setParameter('arrived', OrderItemStatus::CODE_ARRIVED);

            $users = $q->getResult('ListHydrator');

            foreach ($users as &$user) {
                $list = [];
                if (!empty($user['contacts'])) {
                    $parts = explode(',', $user['contacts']);
                    foreach ($parts as $part) {
                        $contacts = explode('|', $part);

                        $list[$contacts[0]][] = $contacts[1];
                    }
                }

                $user['contacts'] = $list;
            }

            return $users;
        }
    }
}