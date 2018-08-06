<?php 

namespace ContentBundle\Bus\CategorySection\Query;

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
            throw new NotFoundHttpException(sprintf('Категория с кодом %d не найдена', $query->categoryId));
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\CategorySection\Query\DTO\Section (
                    cs.id,
                    cs.name 
                )
            FROM ContentBundle:CategorySection cs 
            WHERE cs.categoryId = :categoryId 
            ORDER BY cs.name 
        ");
        $q->setParameter('categoryId', $category->getId());
        $sections = $q->getArrayResult();

        return array_merge([new DTO\Section(0, '')], $sections);
    }
}