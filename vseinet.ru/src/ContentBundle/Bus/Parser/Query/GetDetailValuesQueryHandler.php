<?php 

namespace ContentBundle\Bus\Parser\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserDetail;

class GetDetailValuesQueryHandler extends MessageHandler
{
    public function handle(GetDetailValuesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $detail = $em->getRepository(ParserDetail::class)->find($query->detailId);
        if (!$detail instanceof ParserDetail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $query->detailId));
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Parser\Query\DTO\DetailValue (
                    pdv.id,
                    pdv.value
                )
            FROM ContentBundle:ParserDetailValue pdv
            WHERE pdv.detailId = :detailId
            ORDER BY pdv.value
        ");
        $q->setParameter('detailId', $detail->getId());
        $values = $q->getArrayResult();

        return $values;
    }
}