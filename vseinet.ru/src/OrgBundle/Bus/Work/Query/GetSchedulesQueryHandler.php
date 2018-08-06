<?php

namespace OrgBundle\Bus\Work\Query;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Employee;
use OrgBundle\Entity\EmployeeSchedule;

class GetSchedulesQueryHandler extends MessageHandler
{
    public function handle(GetSchedulesQuery $query)
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

        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($query->id);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');


        /** @var EmployeeSchedule[] $schedules */
        $schedules = $em->createQuery('
                SELECT es
                FROM OrgBundle:EmployeeSchedule AS es
                WHERE es.employeeUserId = :userId
                    AND es.activeSince <= :till AND (es.activeTill >= :since OR es.activeTill IS NULL)
                ORDER BY es.activeSince
            ')
            ->setParameter('userId', $employee->getUserId())
            ->setParameter('since', $query->since)
            ->setParameter('till', $query->till)
            ->getResult();

        return $schedules;
    }
}