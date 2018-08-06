<?php 

namespace ContentBundle\Bus\Template\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\ParserSource;
use ContentBundle\Entity\ParserDetailGroup;

class GetParserDetailsQueryHandler extends MessageHandler
{
    public function handle(GetParserDetailsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена.', $query->categoryId));
        }

        $source = $em->getRepository(ParserSource::class)->find($query->sourceId);
        if (!$source instanceof ParserSource) {
            throw new NotFoundHttpException(sprintf('Истоник парсера %d не найден', $query->sourceId));
        }

        $group = $em->getRepository(ParserDetailGroup::class)->find($query->groupId);
        if (!$group instanceof ParserDetailGroup) {
            throw new NotFoundHttpException(sprintf('Группа характеристик парсера %d не найдена.', $query->groupId));
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Template\Query\DTO\ParserDetail (
                    pd.id,
                    pd.name,
                    pd2p.groupId,
                    pd.isHidden,
                    (
                        SELECT 
                            GROUP_CONCAT(pd2cd.contentDetailId SEPARATOR ',')
                        FROM ContentBundle:ParserDetailToContentDetail pd2cd 
                        WHERE pd2cd.parserDetailId = pd.id 
                    )
                )
            FROM ContentBundle:ParserDetail pd
            INNER JOIN ContentBundle:ParserDetailToProduct pd2p WITH pd2p.detailId = pd.id 
            INNER JOIN ContentBundle:ParserProduct pp WITH pp.id = pd2p.productId
            INNER JOIN ContentBundle:BaseProduct bp WITH bp.id = pp.baseProductId 
            WHERE bp.categoryId = :categoryId AND pd2p.groupId = :groupId AND pp.sourceId = :sourceId
            GROUP BY pd.id, pd2p.groupId
            ORDER BY pd.name
        ");
        $q->setParameter('categoryId', $category->getId());
        $q->setParameter('groupId', $group->getId());
        $q->setParameter('sourceId', $source->getId());

        return $q->getResult();
    }
}