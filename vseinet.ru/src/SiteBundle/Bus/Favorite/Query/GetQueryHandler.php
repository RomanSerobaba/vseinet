<?php 

namespace SiteBundle\Bus\Favorite\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        if ($this->get('user.identity')->isAuthorized()) {
            $q = $em->createQuery("
                SELECT
                    f.baseProductId
                FROM SiteBundle:Favorite f 
                WHERE f.userId = :userId 
            ");
            $q->setParameter('userId', $this->get('user.identity')->getUser()->getId());
            $ids = $q->getResult('ListHydrator');
        } else {
            $ids = $this->get('session')->get('favorites', []);
        }

        if (empty($ids)) {
            return [];
        }

        $cityId = $this->get('city.identity')->getId();
        $criteria = $cityId ? "p.geoCityId = {$cityId}" : "p.geoCityId IS NULL"; 

        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Favorite\Query\DTO\Product (
                    bp.id,
                    bp.name,
                    p.price,
                    bpi.basename
                )
            FROM ContentBundle:BaseProduct AS bp 
            INNER JOIN PricingBundle:Product AS p WITH p.baseProductId = bp.id 
            LEFT OUTER JOIN ContentBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
            WHERE bp.id IN (:ids) AND {$criteria}
            ORDER BY bp.name 
        ");
        $q->setParameter('ids', $ids);

        return $q->getArrayResult();
    }
}
