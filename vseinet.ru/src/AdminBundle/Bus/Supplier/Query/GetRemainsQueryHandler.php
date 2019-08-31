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
                :available::product_availability_code AS product_availability_code,
                ROUND(SUM(grrc.delta * (si.purchase_price - si.bonus_amount)) / SUM(grrc.delta)) AS price,
                NULL AS price_time,
                NULL AS transfered_by,
                NULL AS transfered_at
            FROM base_product AS bp
            INNER JOIN goods_reserve_register_current AS grrc ON grrc.base_product_id = bp.id
            INNER JOIN supply_item AS si ON si.id = grrc.supply_item_id
            WHERE bp.id = :base_product_id
            GROUP BY bp.name
        ', new DTORSM(DTO\Remain::class));
        $q->setParameter('base_product_id', $product->getId());
        $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
        $remains = $q->getResult('DTOHydrator');

        $q = $em->createNativeQuery("
            WITH transfer_info AS (
                SELECT
                    sptl.transfered_at,
                    CONCAT(p.lastname, ' ', p.firstname, ' ', p.secondname) AS transfered_by,
                    sptl.supplier_product_id
                FROM
                    supplier_product_transfer_log AS sptl
                    LEFT OUTER JOIN org_employee AS oe ON oe.user_id = sptl.transfered_by
                    LEFT OUTER JOIN \"user\" AS u ON u.id = oe.user_id
                    LEFT OUTER JOIN person AS p ON p.id = u.person_id
                    LEFT OUTER JOIN supplier_product_transfer_log AS sptl2 ON sptl2.base_product_id = sptl.base_product_id AND (sptl.transfered_at < sptl2.transfered_at OR sptl.transfered_at = sptl2.transfered_at AND sptl.id < sptl2.id)
                WHERE
                    sptl.base_product_id = :base_product_id AND sptl2.id IS NULL
            )
            SELECT
                sp.id,
                s.code AS supplier_code,
                sp.article,
                sp.code,
                sp.name,
                sp.product_availability_code,
                sp.price,
                sp.updated_at AS price_time,
                ti.transfered_by,
                ti.transfered_at
            FROM supplier_product AS sp
            INNER JOIN supplier AS s ON s.id = sp.supplier_id
            INNER JOIN base_product AS bp ON bp.id = sp.base_product_id
            LEFT OUTER JOIN transfer_info AS ti ON ti.supplier_product_id = sp.id
            WHERE bp.canonical_id = :base_product_id
            ORDER BY sp.product_availability_code DESC, sp.price, sp.updated_at DESC
        ", new DTORSM(DTO\Remain::class));
        $q->setParameter('base_product_id', $product->getId());
        $remains = array_merge($remains, $q->getResult('DTOHydrator'));

        return $remains;
    }
}
