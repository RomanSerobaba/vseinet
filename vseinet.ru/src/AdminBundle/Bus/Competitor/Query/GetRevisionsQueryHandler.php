<?php

namespace AdminBundle\Bus\Competitor\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Product;
use AppBundle\Enum\ProductToCompetitorStatus;
use AppBundle\Enum\ProductToCompetitorState;
use AppBundle\Enum\ProductAvailabilityCode;
use AppBundle\Doctrine\ORM\Query\DTORSM;

class GetRevisionsQueryHandler extends MessageHandler
{
    public function handle(GetRevisionsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(Product::class)->findOneBy([
            'baseProductId' => $query->baseProductId,
            'geoCityId' => $this->getGeoCity()->getId(),
        ]);
        if (!$product instanceof Product) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->baseProductId));
        }

        $q = $em->createNativeQuery("
            SELECT
                ptc.id,
                c.name,
                ptc.link,
                ptc.competitor_price,
                ptc.price_time,
                ptc.requested_at,
                ptc.status,
                CASE WHEN ptc.competitor_price IS NOT NULL AND ptc.competitor_price > :price THEN :ice ELSE :warning END AS state,
                ptc.server_response,
                false AS read_only
            FROM product_to_competitor AS ptc
            INNER JOIN competitor AS c ON c.id = ptc.competitor_id
            WHERE ptc.base_product_id = :base_product_id AND ptc.geo_city_id = :geo_city_id AND c.is_active = true
            ORDER BY ptc.price_time
        ", new DTORSM(DTO\Revision::class));
        $q->setParameter('base_product_id', $product->getBaseProductId());
        $q->setParameter('geo_city_id', $this->getGeoCity()->getId());
        $q->setParameter('price', $product->getPrice());
        $q->setParameter('ice', ProductToCompetitorState::ICE);
        $q->setParameter('warning', ProductToCompetitorState::WARNING);
        $revisions = $q->getResult('DTOHydrator');

        $q = $em->createNativeQuery("
            SELECT
                NULL AS id,
                'Citilink' AS name,
                '' AS link,
                sp.competitor_price,
                sp.updated_at AS price_time,
                NULL AS requested_at,
                :completed::product_to_competitor_status AS status,
                CASE WHEN sp.competitor_price IS NOT NULL AND sp.competitor_price > :price THEN :ice ELSE :warning END AS state,
                200 AS server_response,
                true AS read_only
            FROM supplier_product AS sp
            INNER JOIN product AS p ON p.base_product_id = sp.base_product_id
            WHERE sp.competitor_price IS NOT NULL
                AND sp.product_availability_code = :available
                AND sp.supplier_id = :supplier_id
                AND sp.base_product_id = :base_product_id
        ", new DTORSM(DTO\Revision::class));
        $q->setParameter('base_product_id', $product->getBaseProductId());
        $q->setParameter('price', $product->getPrice());
        $q->setParameter('supplier_id', 220);
        $q->setParameter('completed', ProductToCompetitorStatus::COMPLETED);
        $q->setParameter('ice', ProductToCompetitorState::ICE);
        $q->setParameter('warning', ProductToCompetitorState::WARNING);
        $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
        $revisions = array_merge($revisions, $q->getResult('DTOHydrator'));

        return $revisions;
    }
}
