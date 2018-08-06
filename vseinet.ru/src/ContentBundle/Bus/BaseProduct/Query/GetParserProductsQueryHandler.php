<?php 

namespace ContentBundle\Bus\BaseProduct\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;

class GetParserProductsQueryHandler extends MessageHandler
{
    public function handle(GetParserProductsQuery $query)
    {
        $product = $this->getDoctrine()->getManager()->getRepository(BaseProduct::class)->find($query->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException('Товар не найден');
        }

        return $this->getParserProducts($product);
    }

    protected function getParserProducts(BaseProduct $product)
    {
        $query = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\BaseProduct\Query\DTO\ParserProduct(
                    pp.id,
                    ps.code,
                    bp.name,
                    pp.url,
                    ppd.brand,
                    ppd.description
                )
            FROM ContentBundle:ParserProduct pp
            INNER JOIN ContentBundle:BaseProduct bp WITH bp.id = pp.baseProductId
            INNER JOIN ContentBundle:ParserSource ps WITH ps.id = pp.sourceId 
            LEFT OUTER JOIN ContentBundle:ParserProductData ppd WITH ppd.productId = pp.id 
            WHERE pp.baseProductId = :baseProductId AND pp.status = 200
            ORDER BY ps.code, bp.name 
        ");
        $query->setParameter('baseProductId', $product->getId());

        return $query->getArrayResult();
    }
}
