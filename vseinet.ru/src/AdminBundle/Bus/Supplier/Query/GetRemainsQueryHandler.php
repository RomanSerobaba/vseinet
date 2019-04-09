<?php

namespace AdminBundle\Bus\Supplier\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Doctrine\ORM\Query\DTORSM;

class GetRemainsQueryHandler extends MessageHandler
{
    public function handle(GetRemainsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }

        $q = $em->createNativeQuery('
            SELECT
                NULL AS id,
                NULL AS supplier_code,
                NULL AS article,
                NULL AS code,
                bp.name,
                COALESCE(p.product_availability_code, p2.product_availability_code) AS product_availability_code,
                COALESCE(p.price, p2.price) AS price,
                COALESCE(p.price_time, p2.price_time) AS price_time,
                NULL AS transfered_by,
                NULL AS transfered_at
            FROM base_product AS bp
            LEFT OUTER JOIN product AS p ON p.base_product_id = bp.id AND p.geo_city_id = :geo_city_id AND p.product_availability_code = :available
            INNER JOIN product AS p2 ON p2.base_product_id = bp.id
            WHERE bp.id = :base_product_id AND p2.product_availability_code = :available AND p2.geo_city_id = 0
        ', new DTORSM(DTO\Remain::class));
        $q->setParameter('base_product_id', $product->getId());
        $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
        $q->setParameter('geo_city_id', $this->getGeoCity()->getId());
        $remains = $q->getResult('DTOHydrator');

        $q = $em->createNativeQuery("
            SELECT
                sp.id,
                s.code AS supplier_code,
                sp.article,
                sp.code,
                sp.name,
                sp.product_availability_code,
                sp.price,
                sp.updated_at AS price_time,
                CONCAT(p.lastname, ' ', p.firstname, ' ', p.secondname) AS transfered_by,
                sptl.transfered_at
            FROM supplier_product AS sp
            INNER JOIN supplier AS s ON s.id = sp.supplier_id
            LEFT OUTER JOIN supplier_product_transfer_log AS sptl ON sptl.supplier_product_id = sp.id AND sptl.base_product_id = sp.base_product_id
            LEFT OUTER JOIN org_employee AS oe ON oe.user_id = sptl.transfered_by
            LEFT OUTER JOIN \"user\" AS u ON u.id = oe.user_id
            LEFT OUTER JOIN person AS p ON p.id = u.person_id
            WHERE sp.base_product_id = :base_product_id
            ORDER BY sp.product_availability_code DESC, sp.code ASC
        ", new DTORSM(DTO\Remain::class));
        $q->setParameter('base_product_id', $product->getId());
        $remains = array_merge($remains, $q->getResult('DTOHydrator'));

        return $remains;
    }
}
