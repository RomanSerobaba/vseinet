<?php

namespace OrgBundle\Bus\Department\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;

class GetDepartmentInfoQueryHandler extends MessageHandler
{
    public function handle(GetDepartmentInfoQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /** @var DTO\DepartmentInfo[] $department */
        $department = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\Department\Query\DTO\DepartmentInfo (
                        d.number,
                        d.name,
                        d.typeCode,
                        CASE
                          WHEN dd.activeSince IS NOT NULL
                          THEN TRUE
                          ELSE FALSE
                        END,
                        d.salaryDay,
                        d.salaryPaymentType,
                        d.salaryPaymentSource,
                        r.geoPointId
                    )
                FROM OrgBundle:Department AS d
                    INNER JOIN OrgBundle:DepartmentToDepartment AS dd
                        WITH d.id=dd.departmentId
                            AND (d.pid=dd.pid OR (d.pid IS NULL AND dd.pid IS NULL))
                            AND (dd.activeSince IS NULL OR dd.activeSince <= CURRENT_TIMESTAMP())
                            AND (dd.activeTill IS NULL OR dd.activeTill >= CURRENT_TIMESTAMP())
                    LEFT JOIN OrgBundle:Representative AS r
                        WITH d.id = r.departmentId
                WHERE d.id = :departmentId
            ')
            ->setParameter('departmentId', $query->id)
            ->getResult();

        if (count($department) < 1) {
            throw new EntityNotFoundException('Подразделение не найдено');
        }

        return $department[0];
    }
}