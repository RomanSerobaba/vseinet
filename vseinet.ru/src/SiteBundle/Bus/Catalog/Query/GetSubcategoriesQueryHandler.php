<?php 

namespace SiteBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetSubcategoriesQueryHandler extends MessageHandler
{
    public function handle(GetSubcategoriesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Catalog\Query\DTO\Subcategory (
                    c.id,
                    c.name,
                    c.aliasForId,
                    c.countProducts
                )
            FROM ContentBundle:Category c 
            WHERE c.pid = :pid
            ORDER BY c.name 
        ");
        $q->setParameter('pid', $query->pid);
        $subcategories = $q->getArrayResult();

        if ($this->get('user.identity')->isClient()) {
            $subcategories = array_filter($subcategories, function($subcategory) {
                return 0 < $subcategory->countProducts;
            });
        }

        foreach ($subcategories as $subcategory) {
            $q = $em->createQuery("
                SELECT 
                    bpi.basename,
                    RANDOM() HIDDEN ORD 
                FROM ContentBundle:BaseProductImage bpi 
                INNER JOIN ContentBundle:BaseProduct bp WITH bp.id = bpi.baseProductId 
                INNER JOIN ContentBundle:CategoryPath cp WITH cp.id = bp.categoryId 
                WHERE cp.pid = :categoryId 
                ORDER BY ORD 
            ");
            $q->setParameter('categoryId', $subcategory->id);
            $q->setMaxResults(1);
            $image = $q->getOneOrNullResult();
            if (!empty($image))  {
                $subcategory->baseSrc = $image['basename']; 
            }
        }

        return $subcategories;
    }
}
