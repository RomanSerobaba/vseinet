<?php 

namespace ContentBundle\Bus\Template\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\ParserSource;

class GetParserDetailGroupsQueryHandler extends MessageHandler
{
    public function handle(GetParserDetailGroupsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена.', $query->categoryId));
        }

        $source = $em->getRepository(ParserSource::class)->find($query->sourceId);
        if (!$source instanceof ParserSource) {
            throw new NotFoundHttpException(sprintf('Источник парсинга %d не найден', $query->sourceId));
        }
        
        $q = $em->createQuery("
            SELECT
                NEW ContentBundle\Bus\Template\Query\DTO\ParserDetailGroup (
                    pdg.id,
                    pdg.name,
                    pp.sourceId
                ) 
            FROM ContentBundle:ParserDetailGroup pdg
            INNER JOIN ContentBundle:ParserDetailToProduct pd2p WITH pd2p.groupId = pdg.id 
            INNER JOIN ContentBundle:ParserProduct pp WITH pp.id = pd2p.productId
            INNER JOIN ContentBundle:BaseProduct bp WITH bp.id = pp.baseProductId 
            WHERE bp.categoryId = :categoryId AND pp.sourceId = :sourceId 
            GROUP BY pdg.id, pp.sourceId
            ORDER BY pdg.name
        ");
        $q->setParameter('categoryId', $category->getId());
        $q->setParameter('sourceId', $source->getId());

        return $q->getResult();
    }
}