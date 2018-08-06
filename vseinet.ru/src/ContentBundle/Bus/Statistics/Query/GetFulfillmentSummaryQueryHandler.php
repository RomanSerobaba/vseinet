<?php 

namespace ContentBundle\Bus\Statistics\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Manager;
use ContentBundle\Bus\Statistics\Query\DTO\StatsManager;

class GetFulfillmentSummaryQueryHandler extends MessageHandler
{
    public function handle(GetFulfillmentSummaryQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $manager = $em->getRepository(Manager::class)->find($query->managerId);
        if (!$manager instanceof Manager) {
            throw new NotFoundHttpException(sprintf('Менеджер %d не найден', $query->managerId));
        }

        $fromDate = date('Y-m-d 00:00:00', strtotime($query->fromDate ? $query->fromDate->format('Y-m-d') : date('Y-m-1')));
        $toDate = date('Y-m-d 23:59:59', strtotime($query->toDate ? $query->toDate->format('Y-m-d') : date('Y-m-d')));

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Statistics\Query\DTO\FulfillmentSummary (
                    COUNT(DISTINCT fl.baseProductId)
                )
            FROM ContentBundle:FulfillmentLog fl 
            WHERE fl.createdAt BETWEEN :fromDate AND :toDate AND fl.status = 'done' AND fl.managerId = :managerId
        ");
        $q->setParameter('fromDate', $fromDate);
        $q->setParameter('toDate', $toDate);
        $q->setParameter('managerId', $manager->getId());

        return $q->getOneOrNullResult();
    }
}