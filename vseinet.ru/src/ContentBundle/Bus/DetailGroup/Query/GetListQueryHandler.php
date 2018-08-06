<?php 

namespace ContentBundle\Bus\DetailGroup\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена.', $query->categoryId));
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\DetailGroup\Query\DTO\DetailGroup (
                    dg.id,
                    dg.name
                )
            FROM ContentBundle:DetailGroup dg 
            WHERE dg.categoryId = :categoryId 
            ORDER BY dg.sortOrder
        ");
        $q->setParameter('categoryId', $category->getId());

        return $q->getResult();
    }
}