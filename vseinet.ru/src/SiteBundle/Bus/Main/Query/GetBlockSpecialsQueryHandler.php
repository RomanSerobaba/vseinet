<?php 

namespace SiteBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;

class GetBlockSpecialsQueryHandler extends MessageHandler
{
    public function handle(GetBlockSpecialsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT MAX(bp.id)
            FROM ContentBundle:BaseProduct AS bp 
        ");
        try {
            $maxId = $q->getSingleScalarResult();
        } catch (\Exception $e) {
            return [];
      
        }

        $cityId = $this->get('city.identity')->getId();
        $criteria = $cityId ? "p.geoCityId = {$cityId}" : "p.geoCityId IS NULL";

        $products = [];
        while ($query->count--) {
            $q = $em->createQuery("
                SELECT
                    NEW SiteBundle\Bus\Main\Query\DTO\Product (
                        bp.id,
                        bp.name,
                        bp.categoryId,
                        c.name,
                        p.price,
                        bpi.basename 
                    ) 
                FROM ContentBundle:BaseProduct AS bp 
                INNER JOIN ContentBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                INNER JOIN PricingBundle:Product AS p WITH p.baseProductId = bp.id 
                INNER JOIN ContentBundle:CategoryPath AS cp WITH cp.id = bp.categoryId 
                INNER JOIN ContentBundle:Category AS c WITH c.id = cp.id 
                WHERE 
                    bp.id >= RANDOM() * :maxId 
                    AND bp.id NOT IN (:ids) 
                    AND {$criteria} AND p.price > 0
                    AND p.productAvailabilityCode = :available 
                    AND cp.pid = :categoryId 
            ");
            $q->setParameter('maxId', $maxId);
            $q->setParameter('ids', empty($products) ? [0] : array_keys($products));
            $q->setParameter('categoryId', $query->categoryId);
            $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
            $q->setMaxResults(1);
            try {
                $product = $q->getSingleResult();
                $products[$product->id] = $product;
            } catch (\Exception $e) {
                
            }
        }

        return $products;
    }
}
