<?php 

namespace SiteBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
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
                NEW SiteBundle\Bus\Product\Query\DTO\Detail (
                    d.id,
                    d.name,
                    dg.name,
                    d.typeCode,
                    mu.name,
                    dv.value,
                    d2p.value,
                    dm2p.memo
                )
            FROM ContentBundle:Detail AS d 
            INNER JOIN ContentBundle:DetailGroup AS dg WITH dg.id = d.groupId 
            INNER JOIN ContentBundle:DetailToProduct AS d2p WITH d2p.detailId = d.id 
            LEFT OUTER JOIN ContentBundle:DetailValue AS dv WITH dv.id = d2p.valueId 
            LEFT OUTER JOIN ContentBundle:DetailMemoToProduct AS dm2p WITH dm2p.detailId = d.id AND dm2p.baseProductId = d2p.baseProductId
            LEFT OUTER JOIN ContentBundle:MeasureUnit AS mu WITH mu.id = d.unitId
            WHERE d2p.baseProductId = :baseProductId AND d.pid IS NULL
            ORDER BY dg.sortOrder, d.sortOrder
        ");
        $q->setParameter('baseProductId', $baseProduct->getId()); 
        $details = $q->getResult('IndexByHydrator');

        // $q = $em->createQuery("
        //     SELECT 
        //         d.pid,
        //         d2p.value 
        //     FROM ContentBundle:Detail AS d 
        //     INNER JOIN ContentBundle:DetailToProduct AS d2p WITH d2p.detailId = d.id 
        //     WHERE d2p.baseProductId = :baseProductId AND d.pid IS NOT NULL 
        //     ORDER BY d.sortOrder 
        // ");
        // $q->setParameter('baseProductId', $baseProduct->getId());
        // $depends = $q->getArrayResult();
        // if (!empty($depends)) {
        //     $composites = [];
        //     foreach ($depends as $depend) {
        //         $composites[$depend['pid']][] = $depend['value'];
        //     }

        //     foreach ($composites as $id => $composite) {
        //         $details[$id]->value = implode('x', $composite);
        //     }
        // }

        return $details;
    }
}
