<?php

namespace OrgBundle\Bus\DepartmentType\Query;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Entity\ActivityIndex;

class GetActivitiesIndexesQueryHandler extends MessageHandler
{
    public function handle(GetActivitiesIndexesQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var ActivityIndex[] $indices */
        $indices = $em->createQuery('
                SELECT
                    ao
                FROM OrgBundle:ActivityIndex AS ao
                ORDER BY ao.name
            ')
            ->getResult();

        return $indices;
    }
}