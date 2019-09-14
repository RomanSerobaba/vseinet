<?php

namespace AppBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetInfoQueryHandler extends MessageHandler
{
    public function handle(GetInfoQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $geoCity = $this->getGeoCity();

        if (null !== $user) {
            $q = $em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Cart\Query\DTO\ProductInfo (
                        COALESCE(p2.baseProductId, p.baseProductId),
                        COALESCE(p2.price, p.price),
                        bp.minQuantity,
                        c.quantity
                    )
                FROM AppBundle:Cart AS c
                INNER JOIN AppBundle:BaseProduct AS bp WITH bp.id = c.baseProductId
                LEFT JOIN AppBundle:Product AS p2 WITH p2.baseProductId = bp.canonicalId AND p2.geoCityId = :geoCityId
                INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.canonicalId AND p.geoCityId = 0
                WHERE c.userId = :userId
            ");
            $q->setParameter('userId', $user->getId());
            $q->setParameter('geoCityId', $geoCity->getId());
            $products = $q->getResult('IndexByHydrator');
        }
        else {
            $products = $this->get('session')->get('cart', []);
            if (!empty($products)) {
                $q = $em->createQuery("
                    SELECT
                        NEW AppBundle\Bus\Cart\Query\DTO\ProductInfo (
                            COALESCE(p2.baseProductId, p.baseProductId),
                            COALESCE(p2.price, p.price),
                            bp.minQuantity
                        )
                    FROM AppBundle:BaseProduct AS bp
                    LEFT JOIN AppBundle:Product AS p2 WITH p2.baseProductId = bp.canonicalId AND p2.geoCityId = :geoCityId
                    INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.canonicalId AND p.geoCityId = 0
                    WHERE bp.id IN (:ids)
                ");
                $q->setParameter('ids', array_keys($products));
                $q->setParameter('geoCityId', $geoCity->getId());
                foreach ($q->getArrayResult() as $product) {
                    $product->quantity = intval($products[$product->id]['quantity']);
                    $products[$product->id] = $product;
                }
            }
        }

        return new DTO\Info(...$products);
    }
}
