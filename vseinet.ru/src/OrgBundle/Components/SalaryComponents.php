<?php

namespace OrgBundle\Components;

use AppBundle\Entity\User;
use OrgBundle\Components\DTO;
use OrgBundle\Components\Salary\Base\AbstractComponent;
use OrgBundle\Entity\Activity;
use OrgBundle\Entity\ActivityArea;
use OrgBundle\Entity\ActivityHistory;
use OrgBundle\Entity\ActivityIndex;
use OrgBundle\Entity\EmployeeFine;
use OrgBundle\Entity\EmployeeTax;
use OrgBundle\Entity\Salary;

trait SalaryComponents
{
    /**
     * @return \Doctrine\Bundle\DoctrineBundle\Registry
     */
    abstract public function getDoctrine();

    /** @var AbstractComponent[] */
    private static $_Components = [];

    private static $_CalcCount = 0;

    /**
     * @param Activity $activity
     * @return AbstractComponent
     */
    public function getSalaryComponent(Activity $activity)
    {
        if (!isset(self::$_Components[$activity->getActivityObject()->getCode()])) {
            if (!file_exists(__DIR__ . '/Salary/' . $activity->getActivityObject()->getCode() . '.php')) {
                return self::$_Components[$activity->getActivityObject()->getCode()] = null;
            }

            /** @var \Doctrine\ORM\EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $class = '\OrgBundle\Components\Salary\\' . $activity->getActivityObject()->getCode();
            self::$_Components[$activity->getActivityObject()->getCode()] = new $class($em);
        }
        return self::$_Components[$activity->getActivityObject()->getCode()];
    }

    /**
     * @param int|int[] $activityId
     * @param \DateTime|string $date
     * @param int $expirationInterval
     * @param int $calcLimit
     * @return ActivityHistory[]
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getActivityHistories($activityId, $date, $expirationInterval=3600, $calcLimit=5)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        if (!$date)
            $date = new \DateTime();
        if (is_string($date))
            $date = new \DateTime($date);

        $since = $date->format('Y-m-01');
        $till = date('Y-m-d', strtotime('last day of this month ' . $since));

        /** @var Activity[] $activities */
        $activities = $em->createQuery('
                SELECT
                    a,
                    aa,
                    ai,
                    ao
                FROM OrgBundle:Activity AS a
                    LEFT JOIN a.activityIndex AS ai
                    LEFT JOIN a.activityObject AS ao
                    LEFT JOIN a.activityArea AS aa
                WHERE a.id IN (:activityId)
                ORDER BY a.name, a.id
            ')
            ->setParameter('activityId', is_array($activityId) ? $activityId : [intval($activityId)])
            ->getResult();

        /** @var ActivityHistory[] $historiesRes */
        $historiesRes = $em->createQuery('
                SELECT
                    ah
                FROM OrgBundle:ActivityHistory AS ah
                WHERE ah.activityId IN (:activityId)
                    AND ah.date >= :since AND ah.date <= :till
            ')
            ->setParameter('activityId', is_array($activityId) ? $activityId : [intval($activityId)])
            ->setParameter('since', $since)
            ->setParameter('till', $till)
            ->getResult();

        /** @var ActivityHistory[] $histories */
        $histories = [];
        $activHistIds = array_flip(array_map(function($h){return $h->getActivityId();}, $historiesRes));
        $now = new \DateTime();
        self::$_CalcCount = 0;

        foreach ($activities as $activity) {
            if (isset($activHistIds[$activity->getId()])) {
                $history = $historiesRes[$activHistIds[$activity->getId()]];
            } else {
                $history = new ActivityHistory();
                $history->setActivity($activity);
                $history->setDate(new \DateTime($since));
            }

            if (($expirationInterval > 0)
                && (!$history->getCalculatedAt()
                    || ((self::$_CalcCount < $calcLimit) && ($now->getTimestamp() - $history->getCalculatedAt()->getTimestamp() > $expirationInterval)))) {

                /** @var AbstractComponent $component */
                $component = $this->getSalaryComponent($activity);
                $fact = $component->getFact($activity, $since, $till);

                $history->setFactAmount($fact ? $fact[0][$activity->getActivityIndex()->getCode()] : 0);
                $history->setCalculatedAt(new \DateTime());
                $em->persist($history);

                ++self::$_CalcCount;
            }

            $histories[] = $history;
        }

        $em->flush();

        return $histories;
    }

    /**
     * @param User $currentUser
     * @param int $employeeId
     * @param string|\DateTime|null $date
     * @return array
     * @throws \Doctrine\ORM\ORMException
     */
    public function calculation(User $currentUser, int $employeeId, $date=null)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        if (!$date)
            $date = new \DateTime();
        if (is_string($date))
            $date = new \DateTime($date);

        $since = $date->format('Y-m-01');
        $till = date('Y-m-d', strtotime('last day of this month ' . $since));


        $employeeComponent = new EmployeeComponent($em, $currentUser);
        $employee = $employeeComponent->getInfo($employeeId, false);

        if (!$employee)
            return null;

        $areYouAdmin = $employeeComponent->checkAdmin($currentUser->getId());
        $areYouChief = $employeeComponent->checkChiefFor($currentUser->getId(), $employee->departmentId);
        if ($currentUser->getId() != $employee->userId && !$areYouChief) // Не своё и не подчинённого
            return null; // Оно вам надо?

        $wages = $planRates = $pieceRates = [];
        $salary = $could = 0;
        $daysLastCoeff =
            (
                strtotime(
                    ($since == date('Y-m-01')) ?
                        date('Y-m-d H:i:s') :
                        (($since > date('Y-m-01')) ?
                            $since . ' 00:00:00' :
                            $till . ' 23:59:59'))
                - strtotime($since . ' 00:00:00')
            ) / (strtotime($till . ' 23:59:59') - strtotime($since . ' 00:00:00'));


        /** @var EmployeeTax[] $taxes */
        $taxes = $em->createQuery('
                SELECT et
                FROM OrgBundle:EmployeeTax AS et
                WHERE et.employeeUserId = :employeeId
                    AND et.activeSince <= :till
                    AND (et.activeTill IS NULL OR et.activeTill >= :since)
                ORDER BY et.activeSince
            ')
            ->setParameter('employeeId', $employee->userId)
            ->setParameter('since', $since)
            ->setParameter('till', $till)
            ->getResult();


        /** @var Salary[] $salaries */
        $salaries = $em->createQuery('
                SELECT s
                FROM OrgBundle:Salary AS s
                WHERE s.employeeId = :employeeId
                    AND s.date >= :since AND s.date <= :till
                ORDER BY s.date
            ')
            ->setParameter('employeeId', $employee->userId)
            ->setParameter('since', $since)
            ->setParameter('till', $till)
            ->getResult();


        $wagesRange = $em->createQuery('
                SELECT ew
                FROM OrgBundle:EmployeeWage AS ew
                WHERE ew.employeeUserId = :employeeId
                    AND ew.activeSince <= :till
                    AND (ew.activeTill IS NULL OR ew.activeTill >= :since)
                ORDER BY ew.activeSince
            ')
            ->setParameter('employeeId', $employee->userId)
            ->setParameter('since', $since)
            ->setParameter('till', $till)
            ->getArrayResult();


        /** @var DTO\EmployeeActivityHistory[] $salaryActivities */
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
                WHERE esa.employeeUserId = :employeeId
                    AND esa.activeSince <= :till
                    AND (esa.activeTill IS NULL OR esa.activeTill >= :since)
            ')
            ->setParameter('employeeId', $employee->userId)
            ->setParameter('since', $since)
            ->setParameter('till', $till)
            ->getResult();

        $activityIds = array_map(function($i){return $i->activityId;}, $salaryActivities);
        $activityHistories = $this->getActivityHistories($activityIds, $date);
        $historyIds = array_flip(array_map(function($i){return $i->getActivityId();}, $activityHistories));

        foreach ($salaryActivities as &$item) {
            if (!isset($historyIds[$item->activityId]))
                continue;

            /** @var ActivityHistory $history */
            $history = $activityHistories[$historyIds[$item->activityId]];

            /** @var Activity $activity */
            $activity = $history->getActivity();

            $item->activeSince = $item->activeSince->format('Y-m-d') < $since ? new \DateTime($since) : $item->activeSince;
            $item->activeTill = (!$item->activeTill || $item->activeTill->format('Y-m-d') > $till) ? new \DateTime($till) : $item->activeTill;

            if ($item->isPlanned && $item->rate && $wagesRange) {
                $item->coeff = $history->getPlanAmount()
                            ? ($activity->getActivityObject()->getIsNegative() ?
                                  2 - $history->getFactAmount() / $history->getPlanAmount() :
                                  $history->getFactAmount() / $history->getPlanAmount()
                              ) * 100
                            : 0;
                $item->temp = $activity->getActivityObject()->getIsNegative()
                            ? $item->coeff - 100
                            : ($daysLastCoeff ? $item->coeff / $daysLastCoeff - 100 : 0);

                $planRates[] = $item;
            } else {
                if (!$item->isPlanned) {
                    $item->daysCoeff = date('Y-m-d') < $item->activeSince->format('Y-m-d')
                                || $employee->firedAt && $employee->firedAt->format('Y-m-d') <= $item->activeSince->format('Y-m-d') ? 0 :
                        (strtotime(
                                date('Y-m-d') > (
                                    $employee->firedAt && $employee->firedAt->format('Y-m-d') < $item->activeTill->format('Y-m-d')
                                    ? date('Y-m-d', strtotime('-1 day ' . $employee->firedAt->format('Y-m-d')))
                                    : $item->activeTill->format('Y-m-d'))
                                ? (
                                    $employee->firedAt && $employee->firedAt->format('Y-m-d') < $item->activeTill->format('Y-m-d')
                                    ? date('Y-m-d 00:00:00', strtotime('-1 day ' . $employee->firedAt->format('Y-m-d')))
                                    : $item->activeTill->format('Y-m-d 23:59:59'))
                                : $till . ' 23:59:59') - strtotime($item->activeSince->format('Y-m-d 00:00:00'))) / (strtotime($till . ' 23:59:59') - strtotime($since . ' 00:00:00'));
                    $salary += $item->salary = $this->roundPrice(($activity->getActivityArea()->getCode() == ActivityArea::CODE_EMPLOYEE ? 1 : $item->daysCoeff) *
                                                $history->getFactAmount() * $item->rate / ($activity->getActivityIndex()->getMeasure() == ActivityIndex::MEASURE_MONEY ? 100 : 1));
                    $could += $item->salary;
                } else {
                    $item->rate = 0;
                }

                $item->coeff = $history->getPlanAmount() ? ($activity->getActivityObject()->getIsNegative() ? 2 - $history->getFactAmount() / $history->getPlanAmount() : $history->getFactAmount() / $history->getPlanAmount()) * 100 : 0;
                $item->temp  = $history->getPlanAmount() ? ($activity->getActivityObject()->getIsNegative() ? $item->coeff - 100 : ($daysLastCoeff ? $item->coeff / $daysLastCoeff - 100 : 0)) : 0;

                $pieceRates[] = $item;
            }
        }


        foreach ($wagesRange as &$curr) {
            $curr['commonBase'] = $curr['planBase'] + $curr['constantBase'];
            $curr['since'] = $curr['activeSince']->format('Y-m-d') < $since ? new \DateTime($since) : $curr['activeSince'];
            $curr['till'] = !$curr['activeTill'] || $curr['activeTill']->format('Y-m-d') > $till ? new \DateTime($till) : $curr['activeTill'];

            if ($employee->hiredAt && $employee->hiredAt > $curr['since'] && $employee->hiredAt <= $curr['till']) {
                $curr['since'] = $employee->hiredAt;
            }

            if ($employee->firedAt) {
                if ($employee->firedAt <= $curr['since']) {
                    continue;
                } elseif ($employee->firedAt < $curr['till']) {
                    $curr['till'] = new \DateTime(date('Y-m-d', strtotime('-1 day ' . $employee->firedAt->format('Y-m-d'))));
                }
            }

            $curr['coeff'] = $curr['temp'] = $curr['rate'] = 0;
            $curr['daysCoeff'] = date('Y-m-d') < $curr['since'] || $employee->firedAt && $employee->firedAt <= $curr['since']
                ? 0
                : (strtotime(
                        (date('Y-m-d') > (
                            $employee->firedAt && $employee->firedAt < $curr['till']
                                ? strtotime('-1 day ' . $employee->firedAt->format('Y-m-d'))
                                : $curr['till']
                            ))
                            ? (($employee->firedAt && $employee->firedAt < $curr['till'])
                            ? date('Y-m-d 23:59:59', strtotime('-1 day ' . $employee->firedAt->format('Y-m-d')))
                            : $curr['till']->format('Y-m-d 23:59:59'))
                            : date('Y-m-d H:i:s')) - strtotime($curr['since']->format('Y-m-d 00:00:00')))
                / (strtotime($till . ' 23:59:59') - strtotime($since . ' 00:00:00'));

            /** @var DTO\EmployeeActivityHistory $rate */
            foreach ($planRates as $rate) {
                if ($rate->activeSince->format('Y-m-d') <= $curr['till'] && $rate->activeTill->format('Y-m-d') >= $curr['since']) {
                    if ($curr['since'] > $rate->activeSince->format('Y-m-d'))
                        $rate->activeSince = new \DateTime($curr['since']);
                    if ($curr['till'] > $rate->activeTill->format('Y-m-d'))
                        $rate->activeTill = $curr['till'];
                } else {
                    continue;
                }

                $curr['rates'][] = $rate;
                $curr['rate'] += $rate->rate;
                $curr['coeff'] += $rate->coeff * $rate->rate / 100;
                $curr['temp'] += $rate->temp * $rate->rate / 100;
            }

            $curr['temp'] += $curr['rate'] - 100;
            $salary += $curr['planSalary'] = $curr['coeff'] ? $this->roundPrice((
                'soft' == $curr['planFunction'] ?
//                    (atan(2.5 * ((100 + $curr['temp']) / 100  - 1)) * 1.2 /  pi() + 1) * $curr['planBase'] :
                    (atan(2.5 * ((100 + $curr['temp']) / 100  - 1)) * 1.3 /  pi() + 1) * $curr['planBase'] :
                    (100 + $curr['temp']) / 100 * $curr['planBase']
                ) * $curr['daysCoeff']) : 0;
            $could += $planRates ? ($curr['planSalary'] > $this->roundPrice($curr['planBase'] * $curr['daysCoeff']) ? $curr['planSalary'] : $this->roundPrice($curr['planBase'] * $curr['daysCoeff'])) : 0;
            $salary += $curr['constantSalary'] = $this->roundPrice($curr['constantBase'] * $curr['daysCoeff']);
            $could += $curr['constantSalary'];

            $wages[] = $curr;
        }


        /** @var EmployeeFine[] $allFines */
        $allFines = $em->createQuery('
                SELECT
                    ef,
                    ecr
                FROM OrgBundle:EmployeeFine AS ef
                    LEFT JOIN ef.cancelRequest AS ecr
                WHERE ef.employeeId = :employeeId
                    AND ef.date >= :since AND ef.date <= :till
                ORDER BY ef.date, ef.type, ef.id
            ')
            ->setParameter('employeeId', $employee->userId)
            ->setParameter('since', $since)
            ->setParameter('till', $till)
            ->getResult();


        /** @var EmployeeFine[] $hiddenFines */
        $hiddenFines = [];

        /** @var EmployeeFine[][] $dayFines */
        $dayFines = [];
        foreach ($allFines as $fine) {
            if ($fine->getIsHidden()) {
                if (false) { // ($currentUser->isAdmin()) {
                    $hiddenFines[] = $fine;
                } else {
                    continue;
                }
            }

            if (!$fine->getType() || $fine->getType() == 'miscellaneous') {
                $dayFines[$fine->getDate()->format('Y-m-d')]['miscellaneous'][] = $fine;
            } else {
                $dayFines[$fine->getDate()->format('Y-m-d')][$fine->getType()][] = $fine;
            }
        }

        /** @var AttendanceComponent $attendanceComponent */
        $attendanceComponent = new AttendanceComponent($em, $currentUser);

        if ($attendance = $attendanceComponent->getAttendance($employee->userId, $since, $till, true)) {
            $scheduleSummary = $attendance['summary']['schedule'] ? : 745200;
            $timeZero = new \DateTime('1970-01-01 00:00:00');

            foreach ($attendance['days'] as $day) {
                $late = $day['late'] ?? 0;
                $absent = $day['summary']['initial_absent'] ?? 0;
                $overwork = $day['summary']['unpaid'] ?? 0;

                $wage = 0;
                $overWage = 0;

                foreach ($wages as $curr) {
                    if ($day['day'] >= $curr['since']->format('Y-m-d') && $day['day'] <= $curr['till']->format('Y-m-d')) {
                        $wage = $curr['commonBase'];
                        $overWage = $curr['constantBase'] + (isset($curr['rates']) ? $curr['planBase'] : 0);

                        break;
                    }
                }

                if (!$employee->isIrregular) {
                    if ($absent >= 60 || $late) {
                        /** @var EmployeeFine $fine */
                        $fine = $dayFines[$day['day']][EmployeeFine::TYPE_ABSENCE][0] ?? null;
                        $isChangedFine = false;
                        if (!$fine) {
                            $fine = new EmployeeFine();

                            $fine->setEmployeeId($employee->userId);
                            $fine->setType(EmployeeFine::TYPE_ABSENCE);
                            $fine->setDate(new \DateTime($day['day']));
                            $fine->setIsHidden(false);
                            $fine->setStatus(EmployeeFine::STATUS_APPLIED);
                            $fine->setStatusChangedBy($currentUser->getId());
                            $fine->setStatusChangedAt(new \DateTime());

                            $isChangedFine = true;

                            $dayFines[$day['day']][EmployeeFine::TYPE_ABSENCE][] = $fine;
                        }

                        $newTime   = (new \DateTime())->setTimestamp($timeZero->getTimestamp() + $absent);
                        $newAmount = round(-($late ? (10 == $late ? 15000 : 10000) : 0) - 300 * floor($absent / 60), -2);
                        $newCause  = ($late ? 'Опоздание. ' : '') .
                            ($absent >= 60
                                ? 'Прогул' .
                                (floor($absent / 3600) ? ' ' . floor($absent / 3600) . ' ч' : '') .
                                ($absent % 3600 ? ' ' . floor(($absent % 3600) / 60) . ' мин' : '') : '') .
                            ' (депремирование)';

                        if ($fine->getTime() != $newTime) {
                            $fine->setTime($newTime);
                            $isChangedFine = true;
                        }
                        if ($fine->getAmount() != $newAmount) {
                            $fine->setAmount($newAmount);
                            $isChangedFine = true;
                        }
                        if ($fine->getAutoCause() != $newCause) {
                            $fine->setAutoCause($newCause);
                            $isChangedFine = true;
                        }

                        if ($isChangedFine)
                            $em->persist($fine);

                        if ($absent >= 60) {
                            /** @var EmployeeFine $fine */
                            $fine = $dayFines[$day['day']][EmployeeFine::TYPE_UNWORKING][0] ?? null;
                            $isChangedFine = false;
                            if (!$fine) {
                                $fine = new EmployeeFine();

                                $fine->setEmployeeId($employee->userId);
                                $fine->setType(EmployeeFine::TYPE_UNWORKING);
                                $fine->setDate(new \DateTime($day['day']));
                                $fine->setIsHidden(false);
                                $fine->setStatus(EmployeeFine::STATUS_APPLIED);
                                $fine->setStatusChangedBy($currentUser->getId());
                                $fine->setStatusChangedAt(new \DateTime());

                                $isChangedFine = true;

                                $dayFines[$day['day']][EmployeeFine::TYPE_UNWORKING][] = $fine;
                            }

                            $newTime   = (new \DateTime())->setTimestamp($timeZero->getTimestamp() + $absent);
                            $newAmount = -round($absent * $wage / $scheduleSummary, -2);
                            $newCause  = 'Не отработано' .
                                (floor($absent / 3600) ? ' ' . floor($absent / 3600) . ' ч' : '') .
                                ($absent % 3600 ? ' ' . floor(($absent % 3600) / 60) . ' мин' : '') .
                                ' (время)';

                            if ($fine->getTime() != $newTime) {
                                $fine->setTime($newTime);
                                $isChangedFine = true;
                            }
                            if ($fine->getAmount() != $newAmount) {
                                $fine->setAmount($newAmount);
                                $isChangedFine = true;
                            }
                            if ($fine->getAutoCause() != $newCause) {
                                $fine->setAutoCause($newCause);
                                $isChangedFine = true;
                            }

                            if ($isChangedFine)
                                $em->persist($fine);

                        } else {
                            if (isset($dayFines[$day['day']][EmployeeFine::TYPE_UNWORKING][0])) {
                                $em->remove($dayFines[$day['day']][EmployeeFine::TYPE_UNWORKING][0]);
                                unset($dayFines[$day['day']][EmployeeFine::TYPE_UNWORKING]);
                            }
                        }
                    } else {
                        if (isset($dayFines[$day['day']][EmployeeFine::TYPE_ABSENCE][0])) {
                            $em->remove($dayFines[$day['day']][EmployeeFine::TYPE_ABSENCE][0]);
                            unset($dayFines[$day['day']][EmployeeFine::TYPE_ABSENCE]);
                        }
                        if (isset($dayFines[$day['day']][EmployeeFine::TYPE_UNWORKING][0])) {
                            $em->remove($dayFines[$day['day']][EmployeeFine::TYPE_UNWORKING][0]);
                            unset($dayFines[$day['day']][EmployeeFine::TYPE_UNWORKING]);
                        }
                    }
                }

                if (isset($dayFines[$day['day']]) && count($dayFines[$day['day']]) <= 0)
                    unset($dayFines[$day['day']]);

                if (isset($dayFines[$day['day']][EmployeeFine::TYPE_OVERTIME][0])) {
                    /** @var EmployeeFine $fine */
                    $fine = $dayFines[$day['day']][EmployeeFine::TYPE_OVERTIME][0];
                    $isChangedFine = false;
                    $workSeconds = $fine->getTime()->getTimestamp() - $timeZero->getTimestamp();

                    $newAmount = round($workSeconds * $overWage / $scheduleSummary, -2);
                    $newCause  = 'Отработано сверхурочно' .
                        (floor($workSeconds / 3600) ? ' ' . floor($workSeconds / 3600) . ' ч' : '') .
                        (floor(($workSeconds % 3600) / 60) ? ' ' . floor(($workSeconds % 3600) / 60) . ' мин' : '') .
                        ($workSeconds > $overwork + 60
                            ? ' <span class="text-warning">(по базе:' .
                            (floor($overwork / 3600) ? ' ' . floor($overwork / 3600) . ' ч' : '') .
                            ($overwork % 3600 || !$overwork ? ' ' . floor(($overwork % 3600) / 60) . ' мин' : '') .
                            ')</span>'
                            : '') .
                        ' (переработка)';

                    if ($fine->getAmount() != $newAmount) {
                        $fine->setAmount($newAmount);
                        $isChangedFine = true;
                    }
                    if ($fine->getAutoCause() != $newCause) {
                        $fine->setAutoCause($newCause);
                        $isChangedFine = true;
                    }

                    if ($isChangedFine)
                        $em->persist($fine);
                }
            }
        }

        $em->flush();

        $allFines = [];
        foreach ($dayFines as $day => $types) {
            foreach ($types as $type => $fines) {
                /** @var EmployeeFine $fine */
                foreach ($fines as $fine) {
                    $allFines[] = $fine;
                    if ($fine->getStatus() == EmployeeFine::STATUS_APPLIED) {
                        $salary += $fine->getAmount();
                        $could += $fine->getAmount() > 0 ? $fine->getAmount() : 0;
                    }
                }
            }
        }

        $salary = $this->roundPrice($salary - ($salaries ? $salaries[0]->getTax() : 0));
        $could = $this->roundPrice($could);

        return [
            'wages' => $wages,
            'planIndexes' => $planRates,
            'pieceIndexes' => $pieceRates,
            'fines' => $allFines,
            'hasTax' => $taxes ? true : false,
            'taxAmount' => $salaries ? $salaries[0]->getTax() : 0,
//            'planProgress' => 73,
            'salaryAmount' => $salary,
            'idealSalaryAmount' => $could < 0 ? 0 : $could,
            'paidAmount' => $salaries ? $salaries[0]->getPaid() : 0,
//            'hourlyRate' => 200,
            'areYouChief' => $areYouChief,
            'areYouAdmin' => $areYouAdmin



//            'salary' => $salary,
//            'could' => $could < 0 ? 0 : $could,
//            'pieceRates' => $pieceRates,
//            'hourWage' => round($overWage / ($employee['attendance']['schedule'] ? $employee['attendance']['schedule'] / 60 / 60 : 9 * 22), -2),
//            'fines' => $fines,
//            'since' => $since,
//            'till' => $till,
//            'hiddenFines' => $hiddenFines,
//            'debtsAmount' => 9240 == $employeeId ? $this->model('Employee')->getExpiredDebtsSum() : $this->model('Employee')->getExpiredDebtsSum($employeeId),
//            'debtsCriticalAmount' => 9240 == $employeeId ? $this->model('Employee')->getExpiredDebtsSum(0, 1) : $this->model('Employee')->getExpiredDebtsSum($employeeId, 1),
//            'stuckedAmount' => in_array($employeeId, [4980, 79422, 64185, 9240, 2519, 7586]) ? $this->model('Representative')->getStuckedProductsAmount(64185 == $employeeId ? [239] : (4980 == $employeeId ? [239, 159] : (79422 == $employeeId ? 141 : (9240 == $employeeId ? [208, 316, 250] : (7586 == $employeeId ? [316] : [243, 247, 290, 294, 244, 302])))) ) : 0,
//            'stuckedPoints' => in_array($employeeId, [4980, 79422, 64185, 9240, 2519, 7586]) ? implode('.', (64185 == $employeeId ? [239] : (4980 == $employeeId ? [239, 159] : (79422 == $employeeId ? [141] : (9240 == $employeeId ? [208, 316, 250] : (7586 == $employeeId ? [316] : [243, 247, 290, 294, 244, 302])))) )) : '',
//            'ownDebt' => $this->model('Employee')->getExpiredOwnDebt($employeeId),
//            'editable' => $this->forward('/admin/org/structure/isEditable', ['employeeNumber' => $employee['number']]),
//            'isInferior' => $this->forward('/admin/org/structure/isFullInferior', ['employeeNumber' => $employee['number']]),
//            'employeeId' => $employee->userId,
//            'tax' => $employee['tax'],
//            'remain' => $salary - $employee['paid'] > 0 ? $salary - $employee['paid'] : 0,
//            'paid' => $employee['paid'],
//            'unconfirmed_payment' => $employee['unconfirmed_payment'],
//            'employee' => $employeeFromList + [
//                    'editable' => $this->forward('/admin/org/structure/isEditable', ['employeeNumber' => $employeeFromList['number']]),
//                    'inquiry' => $this->forward('/admin/org/structure/getInquiries', ['employeeId' => $employee['user_id'], 'month' => date('Y-m-01', strtotime($date))]),
//                    'missing' => $this->forward('/admin/org/structure/getMissings', ['employeeId' => $employee['user_id']]),
//                    'task' => ['salary' => $this->model('Task')->getNonCompleted($employee['user_id'])],
//                ],
//            'department' => $this->model('Department')->get($employeeFromList['department_id']),
//            'attendance' => $attendance
        ];
    }

    /**
     * @param float $number
     * @param int $precision
     * @return float|int
     */
    public function roundPrice($number, $precision = 50)
    {
        $number = round($number);
        $precision *= 100;

        return $number - $number % $precision + ($number % $precision > $precision / 2 ? $precision : 0);
    }
}