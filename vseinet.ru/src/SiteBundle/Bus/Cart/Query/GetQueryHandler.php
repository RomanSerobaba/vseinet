<?php 

namespace SiteBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $cityId = $this->get('city.identity')->getId();
        $criteria = $cityId ? "p.geoCityId = {$cityId}" : "p.geoCityId IS NULL";

        if ($this->get('user.identity')->isAuthorized()) {
            $q = $em->createQuery("
                SELECT 
                    NEW SiteBundle\Bus\Cart\Query\DTO\Product (
                        bp.id,
                        bp.name,
                        bp.minQuantity,
                        bpi.basename,
                        p.price, 
                        c.quantity
                    )
                FROM SiteBundle:Cart c
                INNER JOIN ContentBundle:BaseProduct AS bp WITH bp.id = c.baseProductId
                LEFT OUTER JOIN ContentBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1 
                INNER JOIN PricingBundle:Product AS p WITH p.baseProductId = bp.id  
                WHERE c.userId = :userId AND {$criteria}
            ");
            $q->setParameter('userId', $this->get('user.identity')->getUser()->getId());
            $products = $q->getResult('ListAssocHydrator');
        }
        else {
            $products = $this->get('session')->get('cart', []);
            if (!empty($products)) {
                $q = $em->createQuery("
                    SELECT 
                        NEW SiteBundle\Bus\Cart\Query\DTO\Product (
                            bp.id, 
                            bp.name,
                            bp.minQuantity,
                            bpi.basename,
                            p.price 
                        )
                    FROM ContentBundle:BaseProduct AS bp
                    LEFT OUTER JOIN ContentBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1 
                    INNER JOIN PricingBundle:Product AS p WITH p.baseProductId = bp.id
                    WHERE bp.id IN (:ids) AND {$criteria}
                ");
                $q->setParameter('ids', array_keys($products));
                foreach ($q->getArrayResult() as $product) {
                    $product->quantity = $products[$product->id]['quantity'];
                    $products[$product->id] = $product;
                }
            }
        }

        return new DTO\Cart(...$products);
    }
}
