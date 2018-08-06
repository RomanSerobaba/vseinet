<?php 

namespace SupplyBundle\Bus\Suppliers\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use AppBundle\Enum\DocumentTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;
use AppBundle\ORM\Query\DTORSM;

class GetShippingQueryHandler extends MessageHandler
{
    /**
     * @param GetShippingQuery $query
     *
     * @return array
     */
    public function handle(GetShippingQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $currentUserId = $query->isTest ? 1 : $this->get('user.identity')->getUser()->getId();

        $q = $em->createNativeQuery('
            SELECT
                s.id /*ид поставщика*/,
                s.code as name /*наименование поставщика*/,
                s.manager_id /*ид менеджера*/,
                COUNT( srr.order_item_id ) AS goods_quantity /*количество товаров к отгрузке*/,
                (
                    SELECT
                        COUNT( sy.id ) 
                    FROM
                        supply AS sy
                        JOIN goods_acceptance_doc AS ga ON ga.parent_doc_id = sy.id 
                        AND ga.parent_doc_type = :supply 
                    WHERE
                        sy.supplier_id = s.id 
                        AND ga.completed_at IS NULL 
                ) AS supplies_quantity /*количество счетов в транзите*/,
                s.order_threshold_time /*крайний срок заказа*/,
                srr.order_delivery_time /*ближайшая дата доставки*/,  
                CASE WHEN srr.is_shipping = TRUE 
                    THEN TRUE 
                    ELSE FALSE 
                END AS is_shipping /*поставщик отгружается*/
            FROM
                supplier AS s
                LEFT JOIN (
                    SELECT
                        srr.order_item_id,
                        s.id AS supplier_id,
                        COALESCE ( srs.order_delivery_time, sr.order_delivery_time ) AS order_delivery_time,
                        srs.is_shipping 
                    FROM
                        supplier AS s
                        JOIN supplier_reserve AS sr ON sr.supplier_id = s.id 
                            AND sr.closed_at IS NULL 
                            AND sr.is_shipping = FALSE 
                        LEFT JOIN supplier_reserve AS srs ON srs.supplier_id = s.id 
                            AND srs.closed_at IS NULL 
                            AND srs.is_shipping = TRUE 
                        LEFT JOIN supplier_reserve_register AS srr ON srr.supplier_reserve_id = COALESCE ( srs.id, sr.id ) 
                    WHERE
                        s.is_active = TRUE 
                    GROUP BY
                        srr.order_item_id,
                        s.id,
                        srs.order_delivery_time,
                        sr.order_delivery_time,
                        srs.is_shipping 
                ) AS srr ON srr.supplier_id = s.id 
            GROUP BY
                s.id,
                srr.order_delivery_time,
                srr.is_shipping 
            ORDER BY
                CASE WHEN s.manager_id = :user_id::INTEGER THEN 0 ELSE 1 END,
                goods_quantity DESC
        ', new DTORSM(\SupplyBundle\Bus\Suppliers\Query\DTO\Suppliers::class));

        $q->setParameter('supply', DocumentTypeCode::SUPPLY);
        $q->setParameter('user_id', $currentUserId);

        return $q->getResult('DTOHydrator');
    }
}