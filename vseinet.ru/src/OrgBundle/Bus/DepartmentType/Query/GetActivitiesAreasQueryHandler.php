<?php

namespace OrgBundle\Bus\DepartmentType\Query;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Entity\ActivityArea;

class GetActivitiesAreasQueryHandler extends MessageHandler
{
    public function handle(GetActivitiesAreasQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var ActivityArea[] $areas */
        $areas = $em->createQuery('
                SELECT
                    ao
                FROM OrgBundle:ActivityArea AS ao
                ORDER BY ao.name
            ')
            ->getResult();

        return $areas;
    }
}