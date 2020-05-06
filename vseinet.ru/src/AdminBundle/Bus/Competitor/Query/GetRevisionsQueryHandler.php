<?php

namespace AdminBundle\Bus\Competitor\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Doctrine\ORM\Query\DTORSM;
use AppBundle\Entity\BaseProduct;
use AppBundle\Enum\ProductToCompetitorState;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
                pp.id,
                c.name,
                pp.code,
                pp.name AS product_name,
                prp.url,
                cp.price,
                cp.updated_at AS completed_at,
                prp.requested_at,
                prp.status,
                CASE WHEN COALESCE(cp.price, 0) = 0 OR c.period IS NOT NULL AND cp.updated_at + (c.period || ' day')::INTERVAL < NOW() OR cp.updated_at IS NULL THEN :void WHEN cp.price >= COALESCE(p2.price, p.price) THEN :ice ELSE :warning END AS state,
                CASE WHEN c.channel = 'site' AND c.parse_strategy = 'product' OR c.channel = 'retail' THEN false ELSE true END AS read_only
            FROM partner_product AS pp
            INNER JOIN competitor_product AS cp ON cp.partner_product_id = pp.id
            LEFT OUTER JOIN parser_product AS prp ON prp.partner_product_id = pp.id
            INNER JOIN base_product AS bp ON bp.id = cp.base_product_id
            LEFT OUTER JOIN product AS p2 ON p2.base_product_id = bp.canonical_id AND p2.geo_city_id = :geo_city_id
            INNER JOIN product AS p ON p.base_product_id = bp.canonical_id AND p.geo_city_id = 0
            INNER JOIN competitor AS c ON c.id = cp.competitor_id
            INNER JOIN competitor_to_geo_city AS c2c ON c2c.competitor_id = c.id
            WHERE bp.canonical_id = :base_product_id AND c2c.geo_city_id = :geo_city_id AND c.is_active = true
            ORDER BY cp.updated_at
        ", new DTORSM(DTO\Revision::class));
        $q->setParameter('base_product_id', $product->getId());
        $q->setParameter('geo_city_id', $this->getGeoCity()->getId());
        $q->setParameter('ice', ProductToCompetitorState::ICE);
        $q->setParameter('warning', ProductToCompetitorState::WARNING);
        $q->setParameter('void', ProductToCompetitorState::VOID);
        $revisions = $q->getResult('DTOHydrator');

        return $revisions;
    }
}
