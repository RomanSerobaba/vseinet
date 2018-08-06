<?php 

namespace ContentBundle\Bus\Detail\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GetByIdsQueryHandler extends MessageHandler
{
    public function handle(GetByIdsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Detail\Query\DTO\DetailItem (
                    d.id,
                    d.name
                )
            FROM ContentBundle:Detail d 
            WHERE d.id IN (:ids) AND d.pid IS NULL
            ORDER BY d.name
        ");
        $q->setParameter('ids', $query->ids);
        $details = $q->getResult();
        if (count($details) != count($query->ids)) {
            throw new BadRequestHttpException('Некоторые характеристики не найдены');
        }

        return $details;
    }
}