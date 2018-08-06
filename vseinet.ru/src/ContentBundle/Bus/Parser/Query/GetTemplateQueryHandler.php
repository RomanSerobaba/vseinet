<?php 

namespace ContentBundle\Bus\Parser\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;

class GetTemplateQueryHandler extends MessageHandler
{
    public function handle(GetTemplateQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %d не найдена', $query->categoryId));
        }

        $where = "bp.categoryId = :categoryId";
        switch ($query->filter) {
            case 'active':
                $where .= " AND pd.isHidden = false";
                break;

            case 'hidden':
                $where .= " AND pd.isHidden = true";
                break;
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Parser\Query\DTO\Source (
                    ps.id,
                    ps.code,
                    ps.alias,
                    ps.url
                )
            FROM ContentBundle:ParserSource ps 
            INNER JOIN ContentBundle:ParserProduct pp WITH pp.sourceId = ps.id 
            INNER JOIN ContentBundle:BaseProduct bp WITH bp.id = pp.baseProductId 
            WHERE {$where}
            GROUP BY ps.id 
            ORDER BY ps.code, ps.alias  
        ");
        $q->setParameter('categoryId', $category->getId());

        $sources = $q->getResult('IndexByHydrator');
        if (empty($sources)) {
            return new DTO\Template();
        }

        $q = $em->createQuery("
            SELECT
                NEW ContentBundle\Bus\Parser\Query\DTO\DetailGroup (
                    pdg.id,
                    pdg.name,
                    pp.sourceId
                ) 
            FROM ContentBundle:ParserDetailGroup pdg
            INNER JOIN ContentBundle:ParserDetailToProduct pd2p WITH pd2p.groupId = pdg.id 
            INNER JOIN ContentBundle:ParserProduct pp WITH pp.id = pd2p.productId
            INNER JOIN ContentBundle:BaseProduct bp WITH bp.id = pp.baseProductId 
            WHERE {$where} 
            GROUP BY pdg.id, pp.sourceId
            ORDER BY pdg.name
        ");
        $q->setParameter('categoryId', $category->getId());

        $groups = $q->getResult('IndexByHydrator');
        foreach ($groups as $group) {
            $sources[$group->sourceId]->groupIds[] = $group->id;
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Parser\Query\DTO\Detail (
                    pd.id,
                    pd.name,
                    pd.groupId,
                    pd.isHidden
                )
            FROM ContentBundle:ParserDetail pd
            INNER JOIN ContentBundle:ParserDetailToProduct pd2p WITH pd2p.detailId = pd.id 
            INNER JOIN ContentBundle:ParserProduct pp WITH pp.id = pd2p.productId
            INNER JOIN ContentBundle:BaseProduct bp WITH bp.id = pp.baseProductId 
            WHERE {$where}
            GROUP BY pd.id
            ORDER BY pd.name
        ");
        $q->setParameter('categoryId', $category->getId());

        $details = $q->getArrayResult();
        foreach ($details as $detail) {
            $groups[$detail->groupId]->detailIds[] = $detail->id;
        }

        return new DTO\Template($sources, $groups, $details);
    }
}