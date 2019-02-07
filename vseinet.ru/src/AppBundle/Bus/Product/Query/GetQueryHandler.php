<?php

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\NoResultException;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT bpml.newId
            FROM AppBundle:BaseProductMergeLog bpml
            WHERE bpml.oldId = :id
        ");
        $q->setParameter('id', $query->id);
        $q->setMaxResults(1);
        try {
            $id = $q->getSingleScalarResult();
        } catch (NoResultException $e) {
            $id = $query->id;
        }

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Product\Query\DTO\Product (
                    bp.id,
                    bp.name,
                    bpd.exname,
                    bp.categoryId,
                    bp.brandId,
                    COALESCE(p.productAvailabilityCode, p2.productAvailabilityCode),
                    COALESCE(p.price, p2.price),
                    COALESCE(p.priceType, p2.priceType),
                    bp.minQuantity,
                    bpd.model,
                    bpd.manufacturerLink,
                    bpd.manualLink,
                    d.description,
                    bp.estimate
                )
            FROM AppBundle:BaseProduct AS bp
            INNER JOIN AppBundle:BaseProductData AS bpd WITH bpd.baseProductId = bp.id
            LEFT OUTER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id AND p.geoCityId = :geoCityId
            INNER JOIN AppBundle:Product AS p2 WITH p2.baseProductId = bp.id
            LEFT OUTER JOIN AppBundle:BaseProductDescription AS d WITH d.baseProductId = bp.id
            WHERE bp.id = :id AND p2.geoCityId = 0
        ");
        $q->setParameter('id', $id);
        $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        $product = $q->getOneOrNullResult();
        if (!$product instanceof DTO\Product) {
            throw new NotFoundHttpException();
        }
        $product->isCanonical = $id == $query->id;

        return $product;
    }
}
