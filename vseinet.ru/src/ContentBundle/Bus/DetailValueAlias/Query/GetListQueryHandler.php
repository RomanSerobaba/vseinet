<?php 

namespace ContentBundle\Bus\DetailValueAlias\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\DetailValue;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $value = $em->getRepository(DetailValue::class)->find($query->valueId);
        if (!$value instanceof DetailValue) {
            throw new NotFoundHttpException(sprintf('Значение характеристики %d не найдено', $query->valueId));
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\DetailValueAlias\Query\DTO\Alias (
                    dva.id,
                    dva.value,
                    dva.valueId 
                )
            FROM ContentBundle:DetailValueAlias dva 
            WHERE dva.valueId = :valueId
            ORDER BY dva.value 
        ");
        $q->setParameter('valueId', $value->getId());
        $aliases = $q->getArrayResult();

        return $aliases;
    }
}