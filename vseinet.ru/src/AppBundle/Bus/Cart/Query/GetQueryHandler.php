<?php 

namespace AppBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $geoCity = $this->getGeoCity();

        if (null !== $user) {
            $q = $em->createQuery("
                SELECT 
                    NEW AppBundle\Bus\Cart\Query\DTO\Product (
                        bp.id,
                        bp.name,
                        bp.minQuantity,
                        bpi.basename,
                        p.price, 
                        c.quantity
                    )
                FROM AppBundle:Cart c
                INNER JOIN AppBundle:BaseProduct AS bp WITH bp.id = c.baseProductId
                LEFT OUTER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1 
                INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id  
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
                        NEW AppBundle\Bus\Cart\Query\DTO\Product (
                            bp.id, 
                            bp.name,
                            bp.minQuantity,
                            bpi.basename,
                            p.price 
                        )
                    FROM AppBundle:BaseProduct AS bp
                    LEFT OUTER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1 
                    INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id
                    WHERE bp.id IN (:ids) AND p.geoCityId = :geoCityId
                ");
                $q->setParameter('ids', array_keys($products));
                $q->setParameter('geoCityId', $geoCity->getId());
                foreach ($q->getResult() as $product) {
                    $product->quantity = $products[$product->id]['quantity'];
                    $products[$product->id] = $product;
                }
            }
        }
        
        return new DTO\Cart($products);
    }
}
