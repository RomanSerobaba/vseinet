<?php

namespace AdminBundle\Bus\Competitor\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Enum\ProductToCompetitorState;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Doctrine\ORM\Query\DTORSM;

class GetRevisionsQueryHandler extends MessageHandler
{
    public function handle(GetRevisionsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }

        $q = $em->createNativeQuery("
            SELECT
                cp.id,
                c.name,
                cp.code,
                cp.name AS product_name,
                cp.url,
                cpp.price,
                cpp.completed_at,
                cpp.requested_at,
                cpp.status,
                CASE WHEN COALESCE(cpp.price, 0) = 0 OR c.period IS NOT NULL AND cpp.completed_at + (c.period || ' day')::INTERVAL < NOW() OR cpp.completed_at IS NULL THEN :void WHEN cpp.price >= COALESCE(p2.price, p.price) THEN :ice ELSE :warning END AS state,
                true AS read_only
                -- CASE WHEN c.channel = 'site' AND c.parse_strategy = 'product' OR c.channel = 'retail' THEN false ELSE true END AS read_only
            FROM competitor_product AS cp
            INNER JOIN base_product AS bp ON bp.id = cp.base_product_id
            LEFT OUTER JOIN product AS p2 ON p2.base_product_id = bp.canonical_id AND p2.geo_city_id = :geo_city_id
            INNER JOIN product AS p ON p.base_product_id = bp.canonical_id AND p.geo_city_id = 0
            INNER JOIN competitor AS c ON c.id = cp.competitor_id
            INNER JOIN competitor_product_geo_city_price AS cpp ON cpp.competitor_product_id = cp.id
            INNER JOIN competitor_to_geo_city AS c2c ON c2c.competitor_id = c.id
            WHERE bp.canonical_id = :base_product_id AND c2c.geo_city_id = :geo_city_id AND c.is_active = true
            ORDER BY cpp.completed_at
        ", new DTORSM(DTO\Revision::class));
        $q->setParameter('base_product_id', $product->getId());
        $q->setParameter('geo_city_id', $this->getGeoCity()->getId());
        $q->setParameter('ice', ProductToCompetitorState::ICE);
        $q->setParameter('warning', ProductToCompetitorState::WARNING);
        $q->setParameter('void', ProductToCompetitorState::VOID);
        $revisions = $q->getResult('DTOHydrator');

        // $q = $em->createNativeQuery("
        //     SELECT
        //         NULL AS id,
        //         'Citilink' AS name,
        //         '' AS link,
        //         sp.competitor_price,
        //         sp.updated_at AS price_time,
        //         NULL AS requested_at,
        //         :completed::product_to_competitor_status AS status,
        //         CASE WHEN COALESCE(sp.competitor_price, 0) = 0 THEN :void WHEN sp.competitor_price > p.price THEN :ice ELSE :warning END AS state,
        //         200 AS server_response,
        //         true AS read_only
        //     FROM supplier_product AS sp
        //     INNER JOIN base_product AS bp ON bp.id = sp.base_product_id
        //     INNER JOIN product AS p ON p.base_product_id = bp.canonical_id AND p.geo_city_id = :geo_city_id
        //     WHERE sp.competitor_price IS NOT NULL
        //         AND sp.product_availability_code = :available
        //         AND sp.supplier_id = :supplier_id
        //         AND bp.canonical_id = :base_product_id
        // ", new DTORSM(DTO\Revision::class));
        // $q->setParameter('base_product_id', $product->getId());
        // $q->setParameter('geo_city_id', $this->getGeoCity()->getId());
        // $q->setParameter('supplier_id', 220);
        // $q->setParameter('completed', ProductToCompetitorStatus::COMPLETED);
        // $q->setParameter('ice', ProductToCompetitorState::ICE);
        // $q->setParameter('warning', ProductToCompetitorState::WARNING);
        // $q->setParameter('void', ProductToCompetitorState::VOID);
        // $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
        // $revisions = array_merge($revisions, $q->getResult('DTOHydrator'));

        return $revisions;
    }
}
