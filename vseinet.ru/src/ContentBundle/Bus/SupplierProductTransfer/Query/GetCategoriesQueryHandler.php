<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;

class GetCategoriesQueryHandler extends MessageHandler 
{
    public function handle(GetCategoriesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $parent = $em->getRepository(Category::class)->find($query->pid);
        if (!$parent instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $query->pid));
        }

        $spec = new Specification\Catalog();
        $where = $spec->build($query->filter, $this->get('user.identity')->getUser()->getCityId());

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\SupplierProductTransfer\Query\DTO\Category (
                    c.id, 
                    c.pid, 
                    c.name, 
                    c.basename,
                    c.gender,
                    c.aliasForId,
                    a.name,
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM ContentBundle:Category cc 
                        WHERE cc.pid = c.id
                    ) THEN false ELSE true END,
                    COUNT(bp.id)
                )
            FROM ContentBundle:Category c 
            INNER JOIN ContentBundle:CategoryPath cp WITH cp.pid = c.id
            LEFT OUTER JOIN ContentBundle:Category a WITH a.id = c.aliasForId
            LEFT OUTER JOIN ContentBundle:BaseProduct bp WITH bp.categoryId = cp.id 
            LEFT OUTER JOIN PricingBundle:Product p WITH p.baseProductId = bp.id 
            WHERE c.pid = :pid AND c.aliasForId IS NULL {$where}
            GROUP BY c.id, a.id
            ORDER BY c.name 
        ");
        $q->setParameter('pid', $parent->getAliasForId() ?: $parent->getId());
        $categories = $q->getArrayResult();

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\SupplierProductTransfer\Query\DTO\Category (
                    c.id, 
                    c.pid, 
                    c.name, 
                    cl.basename,
                    cl.gender,
                    cl.id,
                    cl.name,
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM ContentBundle:Category cc 
                        WHERE cc.pid = c.id
                    ) THEN false ELSE true END,
                    COUNT(bp.id) 
                )
            FROM ContentBundle:Category c 
            INNER JOIN ContentBundle:Category cl WITH cl.id = c.aliasForId
            INNER JOIN ContentBundle:CategoryPath cp WITH cp.pid = cl.id
            LEFT OUTER JOIN ContentBundle:BaseProduct bp WITH bp.categoryId = cp.id 
            LEFT OUTER JOIN PricingBundle:Product p WITH p.baseProductId = bp.id 
            WHERE c.pid = :pid {$where}
            GROUP BY c.id, cl.id
            ORDER BY c.name 
        ");
        $q->setParameter('pid', $parent->getId());
        $categories = array_merge($categories, $q->getArrayResult());

        usort($categories, function($category1, $category2) {
            return strcasecmp($category1->name, $category2->name);
        });

        return $categories;
    }
}