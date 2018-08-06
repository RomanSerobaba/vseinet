<?php 

namespace SiteBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\CategorySeo;

class GetCategoryQueryHandler extends MessageHandler
{
    public function handle(GetCategoryQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Catalog\Query\DTO\Category (
                    c.id,
                    c.name,
                    c.aliasForId,
                    c.countProducts,
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM ContentBundle:Category cc 
                        WHERE cc.pid = c.id OR cc.pid = c.aliasForId
                    ) THEN false ELSE true END,
                    cs.title,
                    cs.description,
                    cs.pageTitle,
                    cs.pageDescription,
                    c.isTplEnabled
                )
            FROM ContentBundle:Category c 
            LEFT OUTER JOIN ContentBundle:CategorySeo cs WITH cs.categoryId = c.id 
            WHERE c.id = :id AND cs.brandId IS NULL
        ");
        $q->setParameter('id', $query->id);
        $category = $q->getSingleResult();
        if (!$category instanceof DTO\Category) {
            throw new NotFoundHttpException();
        } 

        if (0 < $category->id) {
            $q = $em->createQuery("
                SELECT 
                    NEW SiteBundle\Bus\Catalog\Query\DTO\Breadcrumb (
                        c.id,
                        c.name 
                    ),
                    cp.plevel HIDDEN
                FROM ContentBundle:Category c 
                INNER JOIN ContentBundle:CategoryPath cp WITH cp.pid = c.id 
                WHERE cp.id = :id AND cp.id != cp.pid AND c.id > 0
                ORDER BY cp.plevel
            ");
            $q->setParameter('id', $category->id);
            $category->breadcrumbs = $q->getArrayResult();
        }
        
        if (null !== $query->brand) {
            $seo = $em->getRepository(CategorySeo::class)->findOneBy([
                'categoryId' => $category->id,
                'brandId' => $query->brand->id,
            ]);
            if ($seo instanceof CategorySeo) {
                $category->title = $seo->getTitle();
                $category->description = $seo->getDescription();
                $category->pageTitle = $seo->getPageTitle();
                $category->pageDescription = $seo->getPageDescription();
            }
        } 

        return $category;
    }
}
