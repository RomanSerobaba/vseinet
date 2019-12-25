<?php

namespace AppBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\CategorySeo;

class GetCategoryQueryHandler extends MessageHandler
{
    public function handle(GetCategoryQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT
                NEW AppBundle\Bus\Catalog\Query\DTO\Category (
                    c.id,
                    c.name,
                    c.aliasForId,
                    cst.countProducts,
                    CASE WHEN EXISTS (
                        SELECT 1
                        FROM AppBundle:Category cc
                        WHERE cc.pid = c.id OR cc.pid = c.aliasForId
                    ) THEN false ELSE true END,
                    cs.title,
                    cs.description,
                    cs.pageTitle,
                    cs.pageDescription,
                    c.isTplEnabled,
                    c.sefUrl
                )
            FROM AppBundle:Category c
            LEFT OUTER JOIN AppBundle:CategoryStats AS cst WITH cst.categoryId = c.id
            LEFT OUTER JOIN AppBundle:CategorySeo cs WITH cs.categoryId = c.id AND cs.brandId IS NULL
            WHERE c.id = :id
        ");
        $q->setParameter('id', $query->id);
        try {
            $category = $q->getSingleResult();
        } catch (\Exception $e) {
            throw new NotFoundHttpException();
        }

        if (0 < $category->id) {
            $q = $em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Catalog\Query\DTO\Breadcrumb (
                        c.id,
                        c.name,
                        c.sefUrl
                    ),
                    cp.plevel HIDDEN
                FROM AppBundle:Category c
                INNER JOIN AppBundle:CategoryPath cp WITH cp.pid = c.id
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
