<?php

namespace OrgBundle\Bus\DepartmentType\Query;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Entity\ActivityIndex;

class GetActivitiesObjectsQueryHandler extends MessageHandler
{
    public function handle(GetActivitiesObjectsQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var DTO\ActivityObject[] $objects */
        $objects = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\DepartmentType\Query\DTO\ActivityObject (
                        ao.id,
                        ao.name,
                        ao.code,
                        ao.canBeFilteredByCategory,
                        ao.hasInterval,
                        ao.isNegative
                    )
                FROM OrgBundle:ActivityObject AS ao
                ORDER BY ao.name
            ')
            ->getResult();

        $objectsIds = array_flip(array_map(function($o){ return $o->id; }, $objects));


        $indexes = $em->createQuery('
                SELECT
                    ai AS activIndex,
                    GROUP_CONCAT(aoi.activityObjectId) AS objectIds
                FROM OrgBundle:ActivityIndex AS ai
                    INNER JOIN OrgBundle:ActivityObjectToIndex AS aoi
                        WITH ai.id = aoi.activityIndexId
                GROUP BY ai
                ORDER BY ai.name
            ')
            ->getResult();

        foreach ($indexes as $index) {
            /** @var ActivityIndex $objIndex */
            $objIndex = $index['activIndex'];

            $oIds = explode(',', $index['objectIds']);
            foreach ($oIds as $oId) {
                $objects[$objectsIds[$oId]]->indexes[] = $objIndex;
            }
        }


        $areas = $em->createQuery('
                SELECT
                    aa AS activArea,
                    GROUP_CONCAT(aoa.activityObjectId) AS objectIds
                FROM OrgBundle:ActivityArea AS aa
                    INNER JOIN OrgBundle:ActivityObjectToArea AS aoa
                        WITH aa.id = aoa.activityAreaId
                GROUP BY aa
                ORDER BY aa.name
            ')
            ->getResult();

        foreach ($areas as $area) {
            /** @var ActivityIndex $objIndex */
            $objArea = $area['activArea'];

            $oIds = explode(',', $area['objectIds']);
            foreach ($oIds as $oId) {
                $objects[$objectsIds[$oId]]->areas[] = $objArea;
            }
        }

        return $objects;
    }
}