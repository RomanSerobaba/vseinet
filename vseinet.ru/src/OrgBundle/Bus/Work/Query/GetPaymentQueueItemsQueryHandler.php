<?php

namespace OrgBundle\Bus\Work\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetPaymentQueueItemsQueryHandler extends MessageHandler
{
    public function handle(GetPaymentQueueItemsQuery $query) {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var DTO\PaymentQueueItem[] $salaries */
        $salaries = $em->createQuery('
                SELECT
                    NEW OrgBundle\Bus\Work\Query\DTO\PaymentQueueItem (
                        s.employeeId,
                        TRIM(CONCAT(
                            COALESCE(CONCAT(pp.lastname, \' \'), \'\'),
                            COALESCE(CONCAT(pp.firstname, \' \'), \'\'),
                            COALESCE(pp.secondname, \'\')
                        )),
                        s.date,
                        s.queueAmount,
                        fr.id,
                        fr.title
                    )
                FROM OrgBundle:Salary AS s
                    LEFT JOIN AppBundle:User AS uu
                        WITH s.employeeId = uu.id
                    LEFT JOIN AppBundle:Person AS pp
                        WITH uu.personId = pp.id

                    LEFT JOIN OrgBundle:EmployeeToDepartment AS ed
                        WITH s.employeeId = ed.employeeUserId AND ed.isSynthetic = false
                            AND (ed.activeSince IS NULL OR ed.activeSince <= CURRENT_TIMESTAMP())
                            AND (ed.activeTill IS NULL OR ed.activeTill >= CURRENT_TIMESTAMP())
                    LEFT JOIN OrgBundle:Department AS dd
                        WITH ed.departmentId = dd.id

                    LEFT JOIN AccountingBundle:FinancialResource AS fr
                        WITH dd.salaryPaymentSource = fr.id
                WHERE s.queueAmount IS NOT NULL
                ORDER BY s.date, fr.title, pp.lastname, pp.firstname, pp.secondname
            ')
            ->getResult();

        return $salaries;
    }
}