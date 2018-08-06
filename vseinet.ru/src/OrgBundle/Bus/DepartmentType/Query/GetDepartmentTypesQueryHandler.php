<?php

namespace OrgBundle\Bus\DepartmentType\Query;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Entity\DepartmentType;

class GetDepartmentTypesQueryHandler extends MessageHandler
{
    /**
     * @param GetDepartmentTypesQuery $query
     * @return DepartmentType[]
     */
    public function handle(GetDepartmentTypesQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var DepartmentType[] $types */
        $types = $em->createQuery('
                SELECT
                    dt
                FROM OrgBundle:DepartmentType AS dt
                ORDER BY dt.code
            ')
            ->getResult();

        return $types;
    }
}