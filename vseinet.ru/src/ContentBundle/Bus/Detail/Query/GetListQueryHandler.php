<?php 

namespace ContentBundle\Bus\Detail\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\DetailGroup;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(DetailGroup::class)->find($query->groupId);
        if (!$group instanceof DetailGroup) {
            throw new NotFoundHttpException(sprintf('Группа характеристик %d не найдена.', $query->groupId));
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\Detail\Query\DTO\Detail (
                    d.id,
                    d.name,
                    d.groupId,
                    d.typeCode,
                    dt.name,
                    dt.isComposite,
                    mu.measureId,
                    d.unitId,
                    CASE WHEN EXISTS (
                        SELECT 1 
                        FROM ContentBundle:BaseProductNaming bpn 
                        WHERE bpn.detailId = d.id
                    ) 
                    THEN true ELSE false END
                )
            FROM ContentBundle:Detail d 
            INNER JOIN ContentBundle:DetailType dt WITH dt.code = d.typeCode
            LEFT OUTER JOIN ContentBundle:MeasureUnit mu WITH mu.id = d.unitId
            WHERE d.groupId = :groupId AND d.pid IS NULL
            ORDER BY d.sortOrder
        ");
        $q->setParameter('groupId', $group->getId());
        $details = $q->getResult('IndexByHydrator');

        $compositeIds = [];
        foreach ($details as $id => $detail) {
            if ($detail->isComposite) {
                $compositeIds[] = $id;
            }
        }
        if (!empty($compositeIds)) {
            $q = $em->createQuery("
                SELECT 
                    NEW ContentBundle\Bus\Detail\Query\DTO\DetailDepend (
                        d.id,
                        d.name,
                        d.pid 
                    )
                FROM ContentBundle:Detail d 
                WHERE d.pid IN (:compositeIds)
                ORDER BY d.sortOrder 
            ");
            $q->setParameter('compositeIds', $compositeIds);
            $depends = $q->getArrayResult();
            foreach ($depends as $depend) {
                $details[$depend->pid]->depends[] = $depend;
            }
        }

        return array_values($details);
    }
}