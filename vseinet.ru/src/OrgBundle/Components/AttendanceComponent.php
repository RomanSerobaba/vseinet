<?php

namespace OrgBundle\Components;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use OrgBundle\Entity\EmployeeAttendance;
use OrgBundle\Entity\EmployeeFine;
use OrgBundle\Entity\EmployeeSchedule;

class AttendanceComponent
{
    const ATTENDANCE_ABSENCE    = EmployeeFine::TYPE_ABSENCE;
    const ATTENDANCE_OVERTIME   = EmployeeFine::TYPE_OVERTIME;
    const ATTENDANCE_WORKING    = 'working';
    const ATTENDANCE_ASKED      = 'asked';

    /**
     * Entity Manager
     * @var EntityManager
     */
    protected $em;

    /**
     * Current user
     * @var User $currentUser
     */
    protected $user;

    /**
     * AttendanceComponent constructor.
     * @param EntityManager $em
     * @param User $user
     */
    public function __construct(EntityManager $em, User $user)
    {
        $this->em = $em;
        $this->user = $user;
    }

    /**
     * @param int $employeeId
     * @param string $since
     * @param string $till
     * @param bool $collapse
     * @return array
     */
    public function getAttendance(int $employeeId, string $since=null, string $till=null, bool $collapse=null)
    {
        if (!$since) {
            $since = date('Y-m-01');
        }

        if (!$till) {
            $till = date('Y-m-d', strtotime('last day of this month'));
        }

        $employeeComponent = new EmployeeComponent($this->em);
        $employee = $employeeComponent->getInfo($employeeId, false);

        if (!$employee)
            return null;

        $areYouAdmin = $employeeComponent->checkAdmin($this->user->getId());
        $areYouChief = $employeeComponent->checkChiefFor($this->user->getId(), $employee->departmentId);
        if ($this->user->getId() != $employee->userId && !$areYouChief) // Не своё и не подчинённого
            return null; // Оно вам надо?


        $months = [];

        for ($day = $since; $day <= $till; $day = date('Y-m-d', strtotime($day . ' + 1 days'))) {
            $month = date('Y-m', strtotime($day));

            if (!isset($months[$month])) {
                $months[$month] = array_fill_keys(['schedule', 'summary'], []);
            }

            $months[$month]['days'][$day] = array_fill_keys(['schedule', 'attendance', 'summary', 'request', 'all_request'], []);
        }


        /** @var EmployeeSchedule[] $schedules */
        $schedules = $this->em->createQuery('
                SELECT es
                FROM OrgBundle:EmployeeSchedule AS es
                WHERE es.employeeUserId = :userId
                    AND es.activeSince <= :till AND (es.activeTill >= :since OR es.activeTill IS NULL)
                ORDER BY es.activeSince
            ')
            ->setParameter('userId', $employee->userId)
            ->setParameter('since', $since)
            ->setParameter('till', $till)
            ->getResult();

        foreach ($schedules as $item) {
            $start = $since > $item->getActiveSince()->format('Y-m-d')
                ? $since
                : $item->getActiveSince()->format('Y-m-d');

            if (!$employee->firedAt || $start <= $employee->firedAt->format('Y-m-d')) {
                $months[$month]['schedule'][] = $item;

                $end = !$item->getActiveTill() || $till < $item->getActiveTill()->format('Y-m-d')
                    ? $till
                    : $item->getActiveTill()->format('Y-m-d');

                for ($day = $start; $day <= $end; $day = date('Y-m-d', strtotime($day . ' + 1 days'))) {
                    $month = date('Y-m', strtotime($day));
                    $wDay = date('N', strtotime($day));

                    /** @var \DateTime|null $workSince */
                    $workSince = call_user_func([$item, "getS$wDay"]);
                    /** @var \DateTime|null $workTill */
                    $workTill = call_user_func([$item, "getT$wDay"]);

                    if ($workSince) {
                        $workSince->setDate(2001, 1, 1);
                        $workTill->setDate(2001, 1, 1);
                        $scheduleTime = $workTill->getTimestamp() - $workSince->getTimestamp();

                        if (!isset($months[$month]['summary']['schedule'])) {
                            $months[$month]['summary']['schedule'] = $scheduleTime;
                        } else {
                            $months[$month]['summary']['schedule'] += $scheduleTime;
                        }

                        $months[$month]['days'][$day]['summary']['schedule'] = $scheduleTime;

                        if (!$employee->firedAt || $day <= $employee->firedAt->format('Y-m-d')) {
                            $months[$month]['days'][$day]['schedule'][] = [
                                'since' => $workSince->format('H:i:s'),
                                'till' => $workTill->format('H:i:s'),
                                'type' => 'schedule',
                            ];
                            usort($months[$month]['days'][$day]['schedule'], [$this, 'intervalSort']);
                        }
                    }
                }
            }
        }


        /** @var EmployeeFine[] $fines */
        $fines = $this->em->createQuery('
                SELECT
                    ef,
                    ecr
                FROM OrgBundle:EmployeeFine AS ef
                    LEFT JOIN ef.cancelRequest AS ecr
                WHERE ef.employeeId = :userId AND ef.time IS NOT NULL
                    AND ef.type IN (:absence, :unworking, :overtime)
                    AND ef.date >= :since AND ef.date <= :till
                ORDER BY ef.date, ef.type, ef.id
            ')
            ->setParameter('userId', $employee->userId)
            ->setParameter('since', $since)
            ->setParameter('till', $till)
            ->setParameter('absence', EmployeeFine::TYPE_ABSENCE)
            ->setParameter('unworking', EmployeeFine::TYPE_UNWORKING)
            ->setParameter('overtime', EmployeeFine::TYPE_OVERTIME)
            ->getResult();

        foreach ($fines as $fine) {
            if (!$fine->getIsHidden() || $areYouAdmin) {
                $day = date('Y-m-d', $fine->getDate()->getTimestamp());
                $month = date('Y-m', $fine->getDate()->getTimestamp());

                if (($fine->getType() == EmployeeFine::TYPE_OVERTIME && $fine->getStatus() != EmployeeFine::STATUS_APPLIED) ||
                    (in_array($fine->getType(), [EmployeeFine::TYPE_ABSENCE, EmployeeFine::TYPE_UNWORKING]) &&
                        (!$fine->getCancelRequest() || $fine->getCancelRequest()->getStatus() != EmployeeFine::STATUS_APPLIED))) {
                    $months[$month]['days'][$day]['request'][] = $fine->getType();
                }

                $months[$month]['days'][$day]['all_request'][] = $fine;
            }
        }


        /** @var EmployeeAttendance[] $attendances */
        $attendances = $this->em->createQuery('
                SELECT ea
                FROM OrgBundle:EmployeeAttendance AS ea
                WHERE ea.employeeId = :userId
                    AND ea.since >= :since AND ea.since <= :till
                ORDER BY ea.since
            ')
            ->setParameter('userId', $employee->userId)
            ->setParameter('since', $since)
            ->setParameter('till', $till)
            ->getResult();

        foreach ($attendances as $item) {
            $day = $item->getSince()->format('Y-m-d');
            $month = $item->getSince()->format('Y-m');

            if ($day != $item->getTill()->format('Y-m-d')) {
                $months[$month]['days'][$day]['forgotten'] = [
                    'since' => $item->getSince(),
                    'till' => $item->getTill()
                ];
            } else {
                $months[$month]['days'][$day]['attendance'][] = [
                    'since' => $item->getSince()->format('H:i:s'),
                    'till' => $item->getTill()->format('H:i:s'),
                    'type' => 'attendance'
                ];
            }
        }

        if ($employee->onWorkAt
            && ($since <= $employee->onWorkAt->format('Y-m-d'))
            && ($till >= $employee->onWorkAt->format('Y-m-d'))) {
            $months[date('Y-m')]['days'][date('Y-m-d')]['attendance'][] = [
                'since' => $employee->onWorkAt->format('H:i:s'),
                'till' => date('H:i:s'),
                'type' => 'attendance',
            ];
        }


        foreach ($months as $month => $monthData) {
            foreach ($monthData['days'] as $day => $dayData) {
                if ($workIntervals = array_merge(($dayData['schedule'] ?? []), ($dayData['attendance'] ?? []))) {

                    $schedule = ($dayData['schedule'] && isset($dayData['schedule'][0])) ? $dayData['schedule'][0] : [];

                    usort($workIntervals, [$this, 'intervalSort']);
                    $discretes = [];
                    foreach ($workIntervals as $workInterval) {
                        $discretes[$workInterval['since']] = $workInterval['since'];
                        $discretes[$workInterval['till']] = $workInterval['till'];
                    }

                    ksort($discretes);
                    $intervals = [];

                    foreach ($workIntervals as $workInterval) {
                        $previous = null;

                        foreach ($discretes as $timestamp) {
                            if ($timestamp >= $workInterval['since'] && $timestamp <= $workInterval['till']) {
                                if ($previous) {
                                    if (!isset($intervals[$previous . '_' . $timestamp])) {
                                        $intervals[$previous . '_' . $timestamp] = [
                                            'since' => $previous,
                                            'till' => $timestamp,
                                        ];
                                    }

                                    $intervals[$previous . '_' . $timestamp]['types'][$workInterval['type']] = $workInterval['type'];
                                }

                                $previous = $timestamp;
                            }
                        }
                    }

                    usort($intervals, [$this, 'intervalSort']);

                    $attendance = [];
                    $late = 1;

                    if ($day <= date('Y-m-d') && (!$employee->firedAt || $employee->firedAt->format('Y-m-d') >= $day)) {
                        foreach ($intervals as $workInterval) {
                            $type = null;

                            if (isset($workInterval['types']['attendance'])) {
                                if (isset($workInterval['types']['schedule'])) {
                                    $type = self::ATTENDANCE_WORKING;
                                } elseif (isset($workInterval['types']['overtime'])) {
                                    $type = self::ATTENDANCE_OVERTIME;
                                } else {
                                    if (!$schedule || $areYouChief   // Показывать как переработку для руководителя
                                        || $schedule['till'] < $workInterval['till']
                                        && strtotime('2001-01-01 ' . $workInterval['till']) - strtotime('2001-01-01 ' . $schedule['till']) > 60 * 15
                                        || $schedule['since'] > $workInterval['since']
                                        && strtotime('2001-01-01 ' . $schedule['since']) - strtotime('2001-01-01 ' . $workInterval['since']) > 60 * 60) {
                                        $type = self::ATTENDANCE_OVERTIME;
                                    }
                                }

                                $late = 0;
                            } elseif ((isset($workInterval['types']['schedule'])
                                    || isset($workInterval['types']['overtime'])) && ($day != date('Y-m-d')
                                    || $workInterval['since'] < date('H:i:s'))) {
                                if (isset($workInterval['types'][self::ATTENDANCE_ASKED])) {
                                    $type = self::ATTENDANCE_ASKED;
                                } else {
                                    $type = self::ATTENDANCE_ABSENCE;
                                }
                            }

                            if ($type) {
                                $diff = strtotime('2001-01-01 ' . $workInterval['till']) - strtotime('2001-01-01 ' . $workInterval['since']);

                                if ($late && self::ATTENDANCE_ABSENCE == $type) {
                                    $months[$month]['days'][$day]['late'] = $diff >= 600 ? 10 : 1;
                                }

                                if (in_array($type, [self::ATTENDANCE_ASKED, self::ATTENDANCE_ABSENCE]) && $diff < 60) {
                                    continue;
                                }

                                $attendance[] = [
                                    'since' => $workInterval['since'],
                                    'till' => $workInterval['till'],
                                    'type' => $type,
                                ];

                                if (isset($months[$month]['summary'][$type])) {
                                    $months[$month]['summary'][$type] += $diff;
                                } else {
                                    $months[$month]['summary'][$type] = $diff;
                                }

                                if (isset($months[$month]['days'][$day]['summary'][$type])) {
                                    $months[$month]['days'][$day]['summary'][$type] += $diff;
                                } else {
                                    $months[$month]['days'][$day]['summary'][$type] = $diff;
                                }

                                if (self::ATTENDANCE_ABSENCE == $type) {
                                    if (isset($months[$month]['summary']['initial_absent'])) {
                                        $months[$month]['summary']['initial_absent'] += $diff;
                                    } else {
                                        $months[$month]['summary']['initial_absent'] = $diff;
                                    }

                                    if (isset($months[$month]['days'][$day]['summary']['initial_absent'])) {
                                        $months[$month]['days'][$day]['summary']['initial_absent'] += $diff;
                                    } else {
                                        $months[$month]['days'][$day]['summary']['initial_absent'] = $diff;
                                    }
                                }

                                if (self::ATTENDANCE_OVERTIME == $type && (!$schedule
                                        || $schedule['till'] < $workInterval['till']
                                        && strtotime('2001-01-01 ' . $workInterval['till']) - strtotime('2001-01-01 ' . $schedule['till']) > 60 * 15
                                        || $schedule['since'] > $workInterval['since']
                                        && strtotime('2001-01-01 ' . $schedule['since']) - strtotime('2001-01-01 ' . $workInterval['since']) > 60 * 60)) {
                                    if (isset($months[$month]['summary']['pure_unpaid'])) {
                                        $months[$month]['summary']['pure_unpaid'] += $diff;
                                    } else {
                                        $months[$month]['summary']['pure_unpaid'] = $diff;
                                    }

                                    if (isset($months[$month]['days'][$day]['summary']['pure_unpaid'])) {
                                        $months[$month]['days'][$day]['summary']['pure_unpaid'] += $diff;
                                    } else {
                                        $months[$month]['days'][$day]['summary']['pure_unpaid'] = $diff;
                                    }
                                }
                            }
                        }

                        if (isset($months[$month]['days'][$day]['all_request'])) {
                            /** @var EmployeeFine $request */
                            foreach ($months[$month]['days'][$day]['all_request'] as $request) {
                                $seconds = $request->getTime()->getTimestamp() - (new \DateTime('1970-01-01 00:00:00'))->getTimestamp();
                                switch ($request->getType()) {
                                    case self::ATTENDANCE_ABSENCE:
                                        if ($request->getCancelRequest() && $request->getCancelRequest()->getStatus() == EmployeeFine::STATUS_APPLIED) {
                                            if (isset($months[$month]['days'][$day]['summary'][self::ATTENDANCE_ABSENCE])) {
                                                $months[$month]['days'][$day]['summary'][self::ATTENDANCE_ABSENCE] -= $seconds;
                                            } else {
                                                $months[$month]['days'][$day]['summary'][self::ATTENDANCE_ABSENCE] = 0;
                                            }

                                            if (isset($months[$month]['summary'][self::ATTENDANCE_ABSENCE])) {
                                                $months[$month]['summary'][self::ATTENDANCE_ABSENCE] -= $seconds;
                                            } else {
                                                $months[$month]['summary'][self::ATTENDANCE_ABSENCE] = 0;
                                            }

                                            if (isset($months[$month]['days'][$day]['summary'][self::ATTENDANCE_ASKED])) {
                                                $months[$month]['days'][$day]['summary'][self::ATTENDANCE_ASKED] += $seconds;
                                            } else {
                                                $months[$month]['days'][$day]['summary'][self::ATTENDANCE_ASKED] = $seconds;
                                            }

                                            if (isset($months[$month]['summary'][self::ATTENDANCE_ASKED])) {
                                                $months[$month]['summary'][self::ATTENDANCE_ASKED] += $seconds;
                                            } else {
                                                $months[$month]['summary'][self::ATTENDANCE_ASKED] = $seconds;
                                            }
                                        }
                                        break;
                                    case EmployeeFine::TYPE_UNWORKING:
                                        if ($request->getCancelRequest() && $request->getCancelRequest()->getStatus() == EmployeeFine::STATUS_APPLIED) {
                                            if (isset($months[$month]['days'][$day]['summary'][self::ATTENDANCE_WORKING])) {
                                                $months[$month]['days'][$day]['summary'][self::ATTENDANCE_WORKING] += $seconds;
                                            } else {
                                                $months[$month]['days'][$day]['summary'][self::ATTENDANCE_WORKING] = $seconds;
                                            }

                                            if (isset($months[$month]['summary'][self::ATTENDANCE_WORKING])) {
                                                $months[$month]['summary'][self::ATTENDANCE_WORKING] += $seconds;
                                            } else {
                                                $months[$month]['summary'][self::ATTENDANCE_WORKING] = $seconds;
                                            }

                                            if (isset($months[$month]['days'][$day]['summary'][self::ATTENDANCE_ASKED])) {
                                                $months[$month]['days'][$day]['summary'][self::ATTENDANCE_ASKED] -= $seconds;
                                            } else {
                                                $months[$month]['days'][$day]['summary'][self::ATTENDANCE_ASKED] = 0;
                                            }

                                            if (isset($months[$month]['summary'][self::ATTENDANCE_ASKED])) {
                                                $months[$month]['summary'][self::ATTENDANCE_ASKED] -= $seconds;
                                            } else {
                                                $months[$month]['summary'][self::ATTENDANCE_ASKED] = 0;
                                            }
                                        }
                                        break;
                                    case EmployeeFine::TYPE_OVERTIME:
                                        if ($request->getStatus() == EmployeeFine::STATUS_APPLIED) {
                                            if (isset($months[$month]['days'][$day]['summary'][self::ATTENDANCE_OVERTIME])) {
                                                $months[$month]['days'][$day]['summary'][self::ATTENDANCE_OVERTIME] += $seconds;
                                            } else {
                                                $months[$month]['days'][$day]['summary'][self::ATTENDANCE_OVERTIME] = $seconds;
                                            }

                                            if (isset($months[$month]['summary'][self::ATTENDANCE_OVERTIME])) {
                                                $months[$month]['summary'][self::ATTENDANCE_OVERTIME] += $seconds;
                                            } else {
                                                $months[$month]['summary'][self::ATTENDANCE_OVERTIME] = $seconds;
                                            }
                                        }
                                }
                            }
                        }
                    }

                    $previous = null;
                    $previousKey = null;

                    foreach ($attendance as $key2 => $workInterval) {
                        if ($previous) {
                            if ($previous['type'] == $workInterval['type']) {
                                $attendance[$key2]['since'] = $previous['since'];
                                unset($attendance[$previousKey]);
                            }
                        }

                        $previousKey = $key2;
                        $previous = $attendance[$key2];
                    }

                    usort($attendance, [$this, 'intervalSort']);
                    $months[$month]['days'][$day]['attendance'] = $attendance;
                }
            }
        }


        $attendance = [];

        foreach ($months as $month => $monthData) {
            $tmp = [];

            foreach ($monthData['days'] as $day => $curr1) {
                $tmp[] = ($curr1 ? : []) + ['day' => $day];
            }

            $attendance[] = [
                'days' => $tmp,
                'month' => $month,
                'schedule' => $monthData['schedule'],
                'summary' => $monthData['summary'],
                'areYouChief' => $areYouChief,
                'areYouAdmin' => $areYouAdmin
            ];
        }

        if ($collapse && $since == $till) {
            $attendance = $attendance[0]['days'][0];
        } elseif ($collapse && date('Y-m', strtotime($since)) == date('Y-m', strtotime($till))) {
            $attendance = $attendance[0];
        }

        return $attendance;
    }

    static function intervalSort($a, $b)
    {
        if ($a['since'] == $b['since']) {
            if ($a['till'] == $b['till']) {
                return 0;
            }

            return ($a['till'] < $b['till']) ? -1 : 1;
        }

        return ($a['since'] < $b['since']) ? -1 : 1;
    }
}