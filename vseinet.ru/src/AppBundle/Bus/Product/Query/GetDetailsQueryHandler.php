<?php

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;

class GetDetailsQueryHandler extends MessageHandler
{
    public function handle(GetDetailsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
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

        $q = $em->createQuery("
            SELECT
                d2p.parserProductId AS id,
                COUNT(d2p.parserProductId) AS ord
            FROM AppBundle:ParserDetailToProduct AS d2p
            INNER JOIN AppBundle:ParserProduct AS pp WITH pp.id = d2p.parserProductId
            INNER JOIN AppBundle:PartnerProduct AS parp WITH parp.id = pp.targetPartnerProductId
            INNER JOIN AppBundle:BaseProduct AS bp WITH bp.id = parp.baseProductId
            WHERE bp.canonicalId = :baseProductId
            GROUP BY d2p.parserProductId
            ORDER BY ord DESC
        ");
        $q->setParameter('baseProductId', $baseProduct->getId());
        $q->setMaxResults(1);
        $parserProduct = $q->getResult();

        if ($parserProduct) {
            $q = $em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Product\Query\DTO\Detail (
                        d.id,
                        d.name,
                        dg.name,
                        'string',
                        '',
                        dv.value
                    )
                FROM AppBundle:ParserDetail AS d
                INNER JOIN AppBundle:ParserDetailGroup AS dg WITH dg.id = d.parserDetailGroupId
                INNER JOIN AppBundle:ParserDetailToProduct AS d2p WITH d2p.parserDetailId = d.id
                INNER JOIN AppBundle:ParserDetailValue AS dv WITH dv.id = d2p.parserDetailValueId
                INNER JOIN AppBundle:ParserProduct AS pp WITH pp.id = d2p.parserProductId
                INNER JOIN AppBundle:PartnerProduct AS parp WITH parp.id = pp.targetPartnerProductId
                INNER JOIN AppBundle:BaseProduct AS bp WITH bp.id = parp.baseProductId
                WHERE bp.canonicalId = :baseProductId AND pp.id = :parserProductId
                ORDER BY dg.id, d.id
            ");
            $q->setParameter('baseProductId', $baseProduct->getId());
            $q->setParameter('parserProductId', $parserProduct[0]['id']);
            $parserDetails = $q->getResult('IndexByHydrator');
        }

        return count($parserDetails) > count($details) ? $parserDetails : $details;
    }
}
