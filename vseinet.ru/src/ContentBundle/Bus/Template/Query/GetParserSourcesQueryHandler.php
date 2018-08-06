<?php 

namespace ContentBundle\Bus\Template\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;

class GetParserSourcesQueryHandler extends MessageHandler
{
    public function handle(GetParserSourcesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена.', $query->categoryId));
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Template\Query\DTO\ParserSource (
                    ps.id,
                    ps.code,
                    ps.alias,
                    ps.url,
                    s.code,
                    COUNT(DISTINCT pd2cd.parserDetailId),
                    0
                )
            FROM ContentBundle:ParserSource ps 
            INNER JOIN ContentBundle:ParserProduct pp WITH pp.sourceId = ps.id 
            INNER JOIN ContentBundle:BaseProduct bp WITH bp.id = pp.baseProductId  
            INNER JOIN ContentBundle:ParserDetailToProduct pd2p WITH pd2p.productId = pp.id
            LEFT OUTER JOIN ContentBundle:ParserDetailToContentDetail pd2cd WITH pd2cd.parserDetailId = pd2p.detailId
            LEFT OUTER JOIN SupplyBundle:Supplier s WITH s.id = ps.supplierId
            WHERE bp.categoryId = :categoryId
            GROUP BY ps.id, s.id
            ORDER BY ps.code ASC 
        ");
        $q->setParameter('categoryId', $category->getId());
        
        return $q->getResult();
    }
}