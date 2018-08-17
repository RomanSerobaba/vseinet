<?php 

namespace AppBundle\Bus\Category\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Category;

class GetBreadcrumbsQueryHandler extends MessageHandler
{
    public function handle(GetBreadcrumbsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException();
        }

        $q = $em->createQuery("
            SELECT 
                NEW AppBundle\Bus\Product\Query\DTO\Category (
                    c.id,
                    c.name 
                )
            FROM AppBundle:Category AS c 
            INNER JOIN AppBundle:CategoryPath AS cp WITH cp.pid = c.id 
            WHERE cp.id = :id 
            ORDER BY cp.plevel
        ");
        $q->setParameter('id', $category->getId());
        $breadcrumbs = $q->getArrayResult();

        return $breadcrumbs;
    }
}
