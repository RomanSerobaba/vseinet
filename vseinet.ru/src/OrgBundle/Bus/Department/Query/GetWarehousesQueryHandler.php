<?php

namespace OrgBundle\Bus\Department\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetWarehousesQueryHandler extends MessageHandler
{
    public function handle(GetWarehousesQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /** @var DTO\Warehouse[] $rooms */
        $rooms = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\Department\Query\DTO\Warehouse (
                        gr.id,
                        gr.name,
                        gr.type
                    )
                FROM OrgBundle:Representative AS rp
                    INNER JOIN OrgBundle:GeoRoom AS gr
                        WITH rp.geoPointId = gr.geoPointId
                WHERE rp.departmentId = :departmentId
            ')
            ->setParameter('departmentId', $query->id)
            ->getResult();

        return $rooms;
    }
}