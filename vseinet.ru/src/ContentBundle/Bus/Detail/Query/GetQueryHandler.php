<?php 

namespace ContentBundle\Bus\Detail\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Bus\Detail\Query\DTO\Detail\Detail;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Detail\Query\DTO\Detail (
                    d.id,
                    d.name,
                    d.groupId,
                    d.sectionId,
                    d.typeCode,
                    mu.measureId,
                    d.unitId 
                )
            FROM ContentBundle:Detail d 
            LEFT OUTER JOIN ContentBundle:MeasureUnit mu WITH mu.id = d.unitId
            WHERE d.id = :id
        ");
        $q->setParameter('id', $command->id);
        $detail = $q->getOneOrNullResult();
        if (!$detail instanceof Detail) {
            throw new NotFoundHttpException(sprintf('Характеристика %d не найдена', $command->id));
        }

        return $detail;
    }
}