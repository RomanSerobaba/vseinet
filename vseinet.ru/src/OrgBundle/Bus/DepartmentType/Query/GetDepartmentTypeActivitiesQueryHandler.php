<?php

namespace OrgBundle\Bus\DepartmentType\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetDepartmentTypeActivitiesQueryHandler extends MessageHandler
{
    /**
     * @param GetDepartmentTypeActivitiesQuery $query
     * @return DTO\DepartmentTypeActivity[]
     */
    public function handle(GetDepartmentTypeActivitiesQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var DTO\DepartmentTypeActivity[] $activities */
        $activities = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\DepartmentType\Query\DTO\DepartmentTypeActivity (
                        dt.id,
                        dt.code,
                        dtea.id,
                        dtea.name,
                        dtea.activityIndexId,
                        dtea.activityObjectId,
                        dtea.activityAreaId,
                        dtea.categoryId,
                        c.name,
                        dtea.intervalMonth,
                        dtea.isChief,
                        dtea.isDepartment,
                        dtea.coefficient,
                        dtea.isPlanned,
                        dtea.rate
                    )
                FROM OrgBundle:DepartmentTypeEmployeeActivity AS dtea
                    INNER JOIN OrgBundle:DepartmentType AS dt
                        WITH dtea.departmentTypeCode = dt.code
                    LEFT JOIN ContentBundle:Category AS c
                        WITH dtea.categoryId = c.id
                WHERE dt.id = :typeId
                ORDER BY dtea.isChief, dtea.name
            ')
            ->setParameter('typeId', $query->id)
            ->getResult();

        return $activities;
    }
}