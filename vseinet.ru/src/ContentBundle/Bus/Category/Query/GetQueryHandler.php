<?php 

namespace ContentBundle\Bus\Category\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Bus\Category\Query\DTO\Category;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {   
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Category\Query\DTO\Category (
                    c.id,
                    c.name,
                    c.pid,
                    c.aliasForId,
                    a.name,
                    c.basename,
                    c.useExname,
                    c.gender,
                    c.tpl,
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM ContentBundle:Category cc 
                        WHERE cc.pid = c.id OR cc.pid = c.aliasForId
                    ) THEN false ELSE true END
                )
            FROM ContentBundle:Category c 
            LEFT OUTER JOIN ContentBundle:Category a WITH a.id = c.aliasForId
            WHERE c.id = :id 
        ");
        $q->setParameter('id', $query->id);
        $category = $q->getOneOrNullResult();

        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $query->id));
        }

        if ($query->breadcrumbs) {
            $q = $em->createQuery("
                SELECT 
                    NEW ContentBundle\Bus\Category\Query\DTO\Breadcrumb (
                        c.id,
                        c.name
                    )
                FROM ContentBundle:Category c 
                INNER JOIN ContentBundle:CategoryPath cp WITH cp.pid = c.id 
                WHERE cp.id = :id AND cp.pid != cp.id
                ORDER BY cp.plevel 
            ");
            $q->setParameter('id', $category->id);        
            $category->breadcrumbs = $q->getArrayResult();
        }

        if ($category->seo) {
             $q = $em->createQuery("
                SELECT cs 
                FROM ContentBundle:CategorySeo cs
                WHERE cs.categoryId = :categoryId AND (cs.brandId = :brandId OR cs.brandId IS NULL)
                ORDER BY cs.brandId DESC 
            ");
            $q->setParameter('categoryId', $category->aliasForId ?: $category->id);
            $q->setParameter('brandId', $query->brandId);
            $q->setMaxResults(1);
            $category->seo = $q->getOneOrNullResult();     
        }

        return $category;
    }
}