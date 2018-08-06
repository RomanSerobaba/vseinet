<?php

namespace OrgBundle\Bus\Work\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\EmployeeComponent;
use OrgBundle\Entity\EmployeeFine;

class GetAttendanceRequestsQueryHandler extends MessageHandler
{
    /**
     * @param GetAttendanceRequestsQuery $query
     * @return DTO\AttendanceRequest[]
     * @throws EntityNotFoundException
     */
    public function handle(GetAttendanceRequestsQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if (!$query->since) {
            $query->since = date('Y-m-01');
        }

        if (!$query->till) {
            $query->till = date('Y-m-d', strtotime('last day of this month'));
        }


        $employeeComponent = new EmployeeComponent($em);
        $employee = $employeeComponent->getInfo($query->id, false);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');


        /** @var DTO\AttendanceRequest[] $fines */
        $fines = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\Work\Query\DTO\AttendanceRequest (
                        ef.id,
                        ef.type,
                        ef.date,
                        ef.time,
                        ef.amount,
                        ef.cause,
                        ef.autoCause,
                        ef.status,
                        TRIM(CONCAT(
                            COALESCE(CONCAT(pp1.lastname, \' \'), \'\'),
                            COALESCE(CONCAT(pp1.firstname, \' \'), \'\'),
                            COALESCE(pp1.secondname, \'\')
                        )),
                        ef.statusChangedAt,
                        TRIM(CONCAT(
                            COALESCE(CONCAT(pp2.lastname, \' \'), \'\'),
                            COALESCE(CONCAT(pp2.firstname, \' \'), \'\'),
                            COALESCE(pp2.secondname, \'\')
                        )),
                        ef.approvedAt,
                        ecr.cause,
                        ecr.status,
                        TRIM(CONCAT(
                            COALESCE(CONCAT(pp3.lastname, \' \'), \'\'),
                            COALESCE(CONCAT(pp3.firstname, \' \'), \'\'),
                            COALESCE(pp3.secondname, \'\')
                        )),
                        ecr.statusChangedAt,
                        TRIM(CONCAT(
                            COALESCE(CONCAT(pp4.lastname, \' \'), \'\'),
                            COALESCE(CONCAT(pp4.firstname, \' \'), \'\'),
                            COALESCE(pp4.secondname, \'\')
                        )),
                        ecr.approvedAt
                    )
                FROM OrgBundle:EmployeeFine AS ef
                    LEFT JOIN OrgBundle:EmployeeCancelRequest AS ecr
                        WITH ef.id = ecr.fineId

                    LEFT JOIN AppBundle:User AS uu1
                        WITH ef.statusChangedBy = uu1.id
                    LEFT JOIN AppBundle:Person AS pp1
                        WITH uu1.personId = pp1.id

                    LEFT JOIN AppBundle:User AS uu2
                        WITH ef.approvedBy = uu2.id
                    LEFT JOIN AppBundle:Person AS pp2
                        WITH uu2.personId = pp2.id

                    LEFT JOIN AppBundle:User AS uu3
                        WITH ecr.statusChangedBy = uu3.id
                    LEFT JOIN AppBundle:Person AS pp3
                        WITH uu3.personId = pp3.id

                    LEFT JOIN AppBundle:User AS uu4
                        WITH ecr.approvedBy = uu4.id
                    LEFT JOIN AppBundle:Person AS pp4
                        WITH uu4.personId = pp4.id

                WHERE ef.employeeId = :userId
                    AND (ef.type = :overtime OR ef.type IN (:absence, :unworking) AND ecr.fineId IS NOT NULL)
                    AND (ef.isHidden IS NULL OR ef.isHidden = FALSE) 
                    AND ef.date >= :since AND ef.date <= :till
                ORDER BY ef.date, ef.type
            ')
            ->setParameter('userId', $employee->userId)
            ->setParameter('absence', EmployeeFine::TYPE_ABSENCE)
            ->setParameter('unworking', EmployeeFine::TYPE_UNWORKING)
            ->setParameter('overtime', EmployeeFine::TYPE_OVERTIME)
            ->setParameter('since', $query->since)
            ->setParameter('till', $query->till)
            ->getResult();

        return $fines;
    }
}