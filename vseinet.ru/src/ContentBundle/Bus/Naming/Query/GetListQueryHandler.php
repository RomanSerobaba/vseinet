<?php 

namespace ContentBundle\Bus\Naming\Query;

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
                NEW ContentBundle\Bus\Naming\Query\DTO\Item (
                    bpn.id,
                    COALESCE(bpnf.title, d.name),
                    bpn.delimiterBefore,
                    bpn.delimiterAfter,
                    bpn.isRequired,
                    bpn.sortOrder
                )
            FROM ContentBundle:BaseProductNaming bpn 
            LEFT OUTER JOIN ContentBundle:BaseProductNamingField bpnf WITH bpnf.name = bpn.fieldName
            LEFT OUTER JOIN ContentBundle:Detail d WITH d.id = bpn.detailId
            WHERE bpn.categoryId = :categoryId 
            ORDER BY bpn.sortOrder
        ");
        $q->SetParameter('categoryId', $category->getId());

        return $q->getArrayResult();
    }
}