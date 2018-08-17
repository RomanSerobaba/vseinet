<?php 

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Enum\DetailType;

class GetDetailsQueryHandler extends MessageHandler
{
    public function handle(GetDetailsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$baseProduct instanceof BaseProduct){
            throw new NotFoundHttpException();
        }

        $q = $em->createQuery("
            SELECT 
                NEW AppBundle\Bus\Product\Query\DTO\Detail (
                    d.id,
                    d.name,
                    dg.name,
                    d.typeCode,
                    mu.name,
                    dv.value,
                    d2p.value,
                    dm2p.memo
                )
            FROM AppBundle:Detail AS d 
            INNER JOIN AppBundle:DetailGroup AS dg WITH dg.id = d.groupId 
            INNER JOIN AppBundle:DetailToProduct AS d2p WITH d2p.detailId = d.id 
            LEFT OUTER JOIN AppBundle:DetailValue AS dv WITH dv.id = d2p.valueId 
            LEFT OUTER JOIN AppBundle:DetailMemoToProduct AS dm2p WITH dm2p.detailId = d.id AND dm2p.baseProductId = d2p.baseProductId
            LEFT OUTER JOIN AppBundle:MeasureUnit AS mu WITH mu.id = d.unitId
            WHERE d2p.baseProductId = :baseProductId AND d.pid IS NULL
            ORDER BY dg.sortOrder, d.sortOrder
        ");
        $q->setParameter('baseProductId', $baseProduct->getId()); 
        $details = $q->getResult('IndexByHydrator');

        return $details;
    }
}
