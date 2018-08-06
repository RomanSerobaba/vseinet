<?php

namespace OrgBundle\Bus\Work\Query;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Components\DTO\EmployeeActivityHistory;
use OrgBundle\Components\SalaryComponents;

class GetPlanResultsQueryHandler extends MessageHandler
{
    use SalaryComponents;

    /**
     * @param GetPlanResultsQuery $query
     * @return array|DTO\MonthPlans[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function handle(GetPlanResultsQuery $query)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $query->since = new \DateTime(date('Y-m-01', $query->since ? strtotime($query->since) : time()) . ' 00:00:00');
        $query->till  = new \DateTime(date('Y-m-d', strtotime('last day of this month ' .
                ($query->till ? $query->till : date('Y-m-d')))) . ' 23:59:59');

        $objectCodes = [];
        if (!is_array($query->types) || (count($query->types) <= 0)) {
            return [];
        }
        if (in_array('reclamation', $query->types)) {
            $objectCodes += ['Reclamations'];
        }
        if (in_array('overstocked', $query->types)) {
            $objectCodes += ['OverstockedGoods'];
        }
        if (in_array('orders', $query->types)) {
            $objectCodes += ['AllOrders', 'ClientsOrders', 'WholesalersOrders', 'ManagedOrders'];
        }

        $yearAgo =  (new \DateTime($query->till->format('Y-m-15 00:00:00')))->sub(new \DateInterval('P1Y'));
        $yearMonthAgo = (new \DateTime($yearAgo->format('Y-m-15 00:00:00')))->sub(new \DateInterval('P1M'));
        $yearMonthAgo = new \DateTime($yearMonthAgo->format('Y-m-01 00:00:00'));
        $yearAgo = new \DateTime($yearAgo->format('Y-m-01 00:00:00'));

        /** @var DTO\MonthPlans[] $months */
        $months = [];

        if ($yearMonthAgo < $query->since) {
            $month = $yearMonthAgo->format('Y-m');
            $till  = new \DateTime(date('Y-m-d', strtotime('last day of this month ' . $yearMonthAgo->format('Y-m-d'))) . ' 23:59:59');

            $months[] = new DTO\MonthPlans($month, $yearMonthAgo, $till);
        }
        if ($yearAgo < $query->since) {
            $month = $yearAgo->format('Y-m');
            $till  = new \DateTime(date('Y-m-d', strtotime('last day of this month ' . $yearAgo->format('Y-m-d'))) . ' 23:59:59');

            $months[] = new DTO\MonthPlans($month, $yearAgo, $till);
        }

        for ($date = $query->since; $date < $query->till; ) {
            $month = $date->format('Y-m');
            $till  = new \DateTime(date('Y-m-d', strtotime('last day of this month ' . $date->format('Y-m-d'))) . ' 23:59:59');

            $months[] = new DTO\MonthPlans($month, $date, $till);

            $date = new \DateTime(date('Y-m-01', strtotime($till->format('Y-m-d') . ' + 1 day')) . ' 00:00:00');
        }

        /** @var EmployeeActivityHistory[] $salaryActivities */
        $salaryActivities = $em->createQuery('
                SELECT
                    NEW OrgBundle\Components\DTO\EmployeeActivityHistory (
                        esa.id,
                        esa.employeeUserId,
                        esa.activityId,
                        esa.activeSince,
                        esa.activeTill,
                        esa.isPlanned,
                        esa.coefficient,
                        esa.rate
                    )
                FROM OrgBundle:EmployeeSalaryActivity AS esa
                    INNER JOIN OrgBundle:Activity AS a
                        WITH esa.activityId = a.id
                    INNER JOIN a.activityObject AS ao
                        WITH a.activityObjectId = ao.id
                WHERE ao.code IN (:objectCodes)
                    AND esa.activeSince <= :till
                    AND (esa.activeTill IS NULL OR esa.activeTill >= :since)
            ')
            ->setParameter('objectCodes', $objectCodes)
            ->setParameter('since', $query->since)
            ->setParameter('till',  $query->till)
            ->getResult();

        $activityIds = array_map(function($i){return $i->activityId;}, $salaryActivities);

        foreach ($months as &$month) {
            $month->plans = $this->getActivityHistories($activityIds, $month->since, 3600 * 12, 10);
        }

        return $months;
    }
}