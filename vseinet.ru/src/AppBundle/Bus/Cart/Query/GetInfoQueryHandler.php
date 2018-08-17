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
                        p.baseProductId,
                        p.price, 
                        c.quantity
                    )
                FROM AppBundle:Cart c
                INNER JOIN AppBundle:Product p WITH p.baseProductId = c.baseProductId 
                WHERE c.userId = :userId AND p.geoCityId = :geoCityId 
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
                            p.baseProductId, 
                            p.price
                        ) 
                    FROM AppBundle:Product p 
                    WHERE p.baseProductId IN (:ids) AND p.geoCityId = :geoCityId 
                ");
                $q->setParameter('ids', array_keys($products));
                $q->setParameter('geoCityId', $geoCity->getId());
                foreach ($q->getArrayResult() as $product) {
                    $product->quantity = $products[$product->id]['quantity'];
                    $products[$product->id] = $product;
                }
            }
        }

        return new DTO\Info(...$products);
    }
}
