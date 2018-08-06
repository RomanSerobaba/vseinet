<?php 

namespace MatrixBundle\Bus\Template\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MatrixBundle\Entity\TradeMatrixTemplate;

class GetItemsQueryHandler extends MessageHandler
{
    public function handle(GetItemsQuery $query)
    {
        $template = $this->getDoctrine()->getManager()->getRepository(TradeMatrixTemplate::class)->findOneBy(['id' => $query->id]);
        if (!$template instanceof TradeMatrixTemplate) {
            throw new NotFoundHttpException(sprintf('Шаблон %d не найден', $query->id));
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                COUNT(bp.id)
            FROM ContentBundle:BaseProduct AS bp
            JOIN MatrixBundle:TradeMatrixTemplateProduct AS mtp WITH mtp.baseProductId = bp.id
            WHERE mtp.tradeMatrixTemplateId = :templateId
        ");
        $q->setParameter('templateId', $query->id);
        $total = $q->getSingleScalarResult();

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW MatrixBundle\Bus\Template\Query\DTO\BaseProduct (
                    bp.id,
                    bp.name,
                    bp.categoryId,
                    mtp.quantity,
                    bp.supplierPrice
                )
                FROM ContentBundle:BaseProduct AS bp
                JOIN MatrixBundle:TradeMatrixTemplateProduct AS mtp WITH mtp.baseProductId = bp.id
                JOIN ContentBundle:Category AS c WITH bp.categoryId = c.id
                JOIN ContentBundle:CategoryPath AS cp WITH cp.id = c.id AND cp.plevel = 1
                JOIN ContentBundle:Category AS pc WITH cp.pid = pc.id
                WHERE mtp.tradeMatrixTemplateId = :templateId
                ORDER BY pc.name, c.name, bp.name
        ");
        $q->setParameter('templateId', $query->id);
        $q->setMaxResults($query->limit);
        $q->setFirstResult(($query->page - 1) * $query->limit);
        $products = $q->getResult('IndexByHydrator');

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW MatrixBundle\Bus\Template\Query\DTO\Category (
                    c.id,
                    c.name,
                    CASE WHEN cp.plevel > 1 THEN pcp.pid ELSE c.pid END
                )
                FROM ContentBundle:BaseProduct AS bp
                JOIN ContentBundle:CategoryPath AS cp WITH bp.categoryId = cp.id
                JOIN ContentBundle:Category AS c WITH cp.pid = c.id
                LEFT JOIN ContentBundle:CategoryPath AS pcp WITH pcp.id = c.id AND pcp.plevel = 1
                WHERE bp.id IN (:productsIds) AND (cp.id = cp.pid OR cp.plevel <= 1)
        ");
        $q->setParameter('productsIds', array_keys($products));
        $categories = $q->getResult('IndexByHydrator');

        foreach ($categories as $category) {
            if (null !== $category->pid) {
                $categories[$category->pid]->childrenIds[] = $category->id;
            }
        }

        foreach ($products as $product) {
            $categories[$product->categoryId]->productsIds[] = $product->id;
        }

        return new DTO\Items($categories, $products, $total);
    }
}
