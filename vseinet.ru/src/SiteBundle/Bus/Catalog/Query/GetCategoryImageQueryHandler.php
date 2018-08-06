<?php 

namespace SiteBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\CategorySeo;

class GetCategoryImageQueryHandler extends MessageHandler
{
    public function handle(GetCategoryImageQuery $query)
    {
        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Catalog\Query\DTO\Image (
                    bpi.id,
                    bpi.basename 
                ),
                RANDOM() AS HIDDEN ORD
            FROM ContentBundle:BaseProduct AS bp 
            INNER JOIN ContentBundle:BaseProductImage AS bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
            WHERE bp.categoryId = :categoryId 
            ORDER BY ORD 
        ");
        $q->setParameter('categoryId', $query->categoryId);
        $q->setMaxResults(1);
        try {
            $image = $q->getSingleResult();
        } catch (\Exception $e) {
            $image = null;
        }

        return $image;
    }
}
