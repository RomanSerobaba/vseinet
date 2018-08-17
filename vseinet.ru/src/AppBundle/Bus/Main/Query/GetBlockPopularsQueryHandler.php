<?php 

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\ProductAvailabilityCode;

class GetBlockPopularsQueryHandler extends MessageHandler
{
    public function handle(GetBlockPopularsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT MAX(bp.id)
            FROM AppBundle:BaseProduct AS bp 
        ");
        try {
            $maxId = $q->getSingleScalarResult();
        } catch (\Exception $e) {
            return [];
        }

        $products = [];
        $categoryIds = [0];
        while ($query->count -= 1) {
            $q = $em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Main\Query\DTO\Product (
                        bp.id,
                        bp.name,
                        bp.categoryId,
                        c.name,
                        p.price,
                        bpi.basename 
                    ) 
                FROM AppBundle:BaseProduct AS bp 
                INNER JOIN AppBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
                INNER JOIN AppBundle:Product AS p WITH p.baseProductId = bp.id 
                INNER JOIN AppBundle:CategoryPath AS cp WITH cp.id = bp.categoryId 
                INNER JOIN AppBundle:Category AS c WITH c.id = cp.id
                WHERE 
                    bp.id >= RANDOM() * :maxId 
                    AND bp.categoryId NOT IN (:categoryIds) 
                    AND p.price > 0 AND p.geoCityId = :geoCityId
                    AND p.productAvailabilityCode = :available 
            ");
            $q->setParameter('maxId', $maxId);
            $q->setParameter('categoryIds', $categoryIds);
            $q->setParameter('geoCityId', $this->getGeoCity()->getRealId());
            $q->setParameter('available', ProductAvailabilityCode::AVAILABLE);
            $q->setMaxResults(1);
            try {
                $product = $q->getSingleResult();
                $products[] = $product;
                $categoryIds[] = $product->categoryId;
            } catch (\Exception $e) {
                $query->count += 1;   
            }
        }

        return $products;
    }
}
