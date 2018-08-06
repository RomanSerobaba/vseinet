<?php 

namespace ContentBundle\Bus\BaseProduct\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Bus\BaseProduct\Query\DTO\BaseProduct;

class GetByBarcodeQueryHandler extends MessageHandler
{
    public function handle(GetByBarcodeQuery $query)
    {
        return $this->getBaseProduct($query->barCode);
    }

    protected function getBaseProduct($barCode)
    {
        $query = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\BaseProduct\Query\DTO\BaseProduct(
                    bp.id,
                    bp.name,
                    bp.categoryId,
                    bp.sectionId,
                    bp.brandId,
                    bp.colorCompositeId,
                    bpdt.model,
                    bpdt.exname,
                    bpdt.manufacturerLink,
                    bpdt.manualLink,
                    COALESCE(bpd.description, '')
                )
            FROM ContentBundle:BaseProductBarCode bc 
            INNER JOIN ContentBundle:BaseProduct bp WITH bc.baseProductId = bp.id
            INNER JOIN ContentBundle:BaseProductData bpdt WITH bpdt.baseProductId = bp.id 
            LEFT OUTER JOIN ContentBundle:BaseProductDescription bpd WITH bpd.baseProductId = bp.id 
            WHERE
                bc.barCode = :barCode and
                bc.baseProductId is not null
        ");
        $query->setParameter('barCode', $barCode);

        return $query->getArrayResult();
    }
}
