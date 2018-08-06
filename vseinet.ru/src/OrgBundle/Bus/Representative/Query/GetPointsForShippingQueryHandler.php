<?php

namespace OrgBundle\Bus\Representative\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\ORM\Query\DTORSM;

class GetPointsForShippingQueryHandler extends MessageHandler
{
    public function handle(GetPointsForShippingQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $q = $em->createNativeQuery('
            SELECT
                vgp.id AS id /* ид точки */,
                CONCAT ( vgp.geo_city, \' / \', vgp.code ) AS name /* наименование точки */
            FROM (
                SELECT
                    o.geo_point_id 
                FROM
                    supplier_reserve AS sr
                    LEFT JOIN supplier_reserve AS ssr ON ssr.supplier_id = sr.supplier_id 
                        AND ssr.is_shipping = TRUE 
                        AND ssr.closed_at IS NULL 
                    JOIN supplier_reserve_register AS srr ON COALESCE ( ssr.id, sr.id ) = srr.supplier_reserve_id
                    JOIN order_item AS oi ON oi.id = srr.order_item_id
                    JOIN "order" AS o ON o.id = oi.order_id 
                WHERE
                    sr.supplier_id = :supplier_id 
                    AND srr.supply_id IS NULL 
                    AND sr.is_shipping = FALSE 
                    AND sr.closed_at IS NULL 
                GROUP BY
                    o.geo_point_id 
                HAVING
                    SUM( srr.delta ) > 0 
                ) AS srr
                JOIN view_geo_point AS vgp ON vgp.id = srr.geo_point_id 
            ORDER BY
                vgp.name
        ', new DTORSM(\OrgBundle\Bus\Representative\Query\DTO\PointsForShipping::class));

        $q->setParameter('supplier_id', $query->supplierId);

        return $q->getResult('DTOHydrator');
    }
}