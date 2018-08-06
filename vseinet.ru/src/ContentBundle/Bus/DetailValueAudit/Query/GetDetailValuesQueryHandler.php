<?php 

namespace ContentBundle\Bus\DetailValueAudit\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Detail;

class GetDetailValuesQueryHandler extends MessageHandler
{
    public function handle(GetDetailValuesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $detail = $em->getRepository(Detail::class)->find($query->detailId);
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $query->detailId));
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\DetailValueAudit\Query\DTO\DetailValue (
                    dv.id,
                    dv.value,
                    dv.detailId,
                    TRIM(CONCAT_WS(' ', p.firstname, p.secondname, p.lastname)),
                    dv.createdAt,
                    dv.isVerified,
                    CASE WHEN EXISTS (
                        SELECT 1
                        FROM ContentBundle:DetailToProduct d2p 
                        WHERE d2p.valueId = dv.id
                    ) 
                    THEN true ELSE false END
                )
            FROM ContentBundle:DetailValue dv 
            LEFT OUTER JOIN AppBundle:User u WITH u.id = dv.createdBy 
            LEFT OUTER JOIN AppBundle:Person p WITH p.id = u.personId 
            WHERE dv.detailId = :detailId
            ORDER BY dv.value 
        ");
        $q->setParameter('detailId', $query->detailId);
        $values = $q->getResult('IndexByHydrator');
        
        if (empty($values)) {
            return new DTO\DetailValues();
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\DetailValueAudit\Query\DTO\DetailValueAlias (
                    dva.id,
                    dva.value,
                    dva.valueId 
                )
            FROM ContentBundle:DetailValueAlias dva 
            WHERE dva.valueId IN (:valueIds)
            ORDER BY dva.value 
        ");
        $q->setParameter('valueIds', array_keys($values));
        $aliases = $q->getArrayResult();

        foreach ($aliases as $alias) {
            $values[$alias->valueId]->aliasIds[] = $alias->id;
        }

        return new DTO\DetailValues($values, $aliases);
    }
}