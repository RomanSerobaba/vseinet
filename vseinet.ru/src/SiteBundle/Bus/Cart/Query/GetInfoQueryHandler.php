<?php 

namespace SiteBundle\Bus\Cart\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetInfoQueryHandler extends MessageHandler
{
    public function handle(GetInfoQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $cityId = $this->get('session')->get('geo_city_id');
        $criteria = $cityId ? "p.geoCityId = {$cityId}" : "p.geoCityId IS NULL";

        if ($this->get('user.identity')->isAuthorized()) {
            $user = $this->get('user.identity')->getUser();
            $q = $em->createQuery("
                SELECT 
                    NEW SiteBundle\Bus\Cart\Query\DTO\ProductInfo (
                        p.baseProductId,
                        p.price, 
                        c.quantity
                    )
                FROM SiteBundle:Cart c
                INNER JOIN PricingBundle:Product p WITH p.baseProductId = c.baseProductId 
                WHERE c.userId = :userId AND {$criteria} 
            ");
            $q->setParameter('userId', $user->getId());
            $products = $q->getResult('IndexByHydrator');
        }
        else {
            $products = $this->get('session')->get('cart', []);
            if (!empty($products)) {
                $q = $em->createQuery("
                    SELECT 
                        NEW SiteBundle\Bus\Cart\Query\DTO\ProductInfo (
                            p.baseProductId, 
                            p.price
                        ) 
                    FROM PricingBundle:Product p 
                    WHERE p.baseProductId IN (:ids) AND {$criteria}
                ");
                $q->setParameter('ids', array_keys($products));
                foreach ($q->getArrayResult() as $product) {
                    $product->quantity = $products[$product->id]['quantity'];
                    $products[$product->id] = $product;
                }
            }
        }

        return new DTO\Info(...$products);
    }
}
