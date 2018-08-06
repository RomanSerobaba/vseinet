<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\EmployeeComponent;
use OrgBundle\Entity\EmployeeSchedule;

class AddEmployeeScheduleCommandHandler extends MessageHandler
{
    /**
     * @param AddEmployeeScheduleCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(AddEmployeeScheduleCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $employeeComponent = new EmployeeComponent($em);
        $employee = $employeeComponent->getInfo($command->id, false);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');

        if (!$command->activeSince)
            $command->activeSince = date('Y-m-d');

        if ($command->activeTill && ($command->activeTill < $command->activeSince))
            $command->activeTill = $command->activeSince;


        $qText = '
            SELECT es
            FROM OrgBundle:EmployeeSchedule AS es
            WHERE es.employeeUserId = :userId
                AND (es.activeTill >= :since OR es.activeTill IS NULL)';
        $qParam = [
            'userId' => $employee->userId,
            'since' => $command->activeSince
        ];
        if ($command->activeTill) {
            $qText .= '
                AND es.activeSince <= :till';
            $qParam['till'] = $command->activeTill;
        }
        $qText .= '
            ORDER BY es.activeSince';

        /** @var EmployeeSchedule[] $schedules */
        $schedules = $em->createQuery($qText)
            ->setParameters($qParam)
            ->getResult();

        $schedule = null;
        foreach ($schedules as $item) {
            $since = $item->getActiveSince()->format('Y-m-d');
            $till = $item->getActiveTill() ? $item->getActiveTill()->format('Y-m-d') : null;

            // Время действия этого элемента заканчивается до начала нового?
            if ($till && ($till < $command->activeSince))
                continue;

            // Не закончился старый отрезок, но уже начался новый?
            if ($since < $command->activeSince) {

                // Старый отрезок заканчивается после окончания нового?
                if ($command->activeTill && (!$till || ($till > $command->activeTill))) {

                    // Добавляем копию старого отрезка после окончания нового
                    $itemNew = new EmployeeSchedule();
                    $itemNew->setEmployeeUserId($employee->userId);

                    $tm = new \DateTime();
                    $tm->setTimestamp(strtotime($command->activeTill . ' + 1 days'));
                    $itemNew->setActiveSince($tm);
                    $itemNew->setActiveTill($item->getActiveTill());

                    $itemNew->setCreatedBy($item->getCreatedBy());
                    $itemNew->setCreatedAt($item->getCreatedAt());
                    $itemNew->setIsIrregular($item->getIsIrregular());

                    $itemNew->setS1($item->getS1());
                    $itemNew->setT1($item->getT1());
                    $itemNew->setS2($item->getS2());
                    $itemNew->setT2($item->getT2());
                    $itemNew->setS3($item->getS3());
                    $itemNew->setT3($item->getT3());
                    $itemNew->setS4($item->getS4());
                    $itemNew->setT4($item->getT4());
                    $itemNew->setS5($item->getS5());
                    $itemNew->setT5($item->getT5());
                    $itemNew->setS6($item->getS6());
                    $itemNew->setT6($item->getT6());
                    $itemNew->setS7($item->getS7());
                    $itemNew->setT7($item->getT7());

                    $em->persist($itemNew);
                }

                // Устанавливаем конец старого перед началом нового
                $tm = new \DateTime();
                $tm->setTimestamp(strtotime($command->activeSince . ' - 1 days'));
                $item->setActiveTill($tm);

                $em->persist($item);

            // Старый отрезок внутри нового?
            } elseif (!$command->activeTill || ($till && ($till <= $command->activeTill))) {

                if (!$schedule) {
                    // Первый из внутренних отрезков используем для нового расписания
                    $schedule = $item;

                    $schedule->setActiveSince(new \DateTime($command->activeSince));
                    if ($command->activeTill)
                        $schedule->setActiveTill(new \DateTime($command->activeTill));

                } else {

                    // Остальные внутренние - удаляем
                    $em->remove($item);
                }

            // Старый отрезок начинается во время действия нового
            } elseif ($since <= $command->activeTill) {
                // Устанавливаем начало старого после окончания нового
                $tm = new \DateTime();
                $tm->setTimestamp(strtotime($command->activeTill . ' - 1 days'));
                $item->setActiveSince($tm);

                $em->persist($item);
            }
        }

        if (!$schedule) {
            $schedule = new EmployeeSchedule();
            $schedule->setEmployeeUserId($employee->userId);

            $schedule->setActiveSince(new \DateTime($command->activeSince));
            if ($command->activeTill)
                $schedule->setActiveTill(new \DateTime($command->activeTill));
        }

        $schedule->setCreatedBy($currentUser->getId());
        $schedule->setCreatedAt(new \DateTime());
        $schedule->setIsIrregular($command->isIrregular ? true : false);

        if ($command->s1)
            $schedule->setS1(new \DateTime($command->s1));
        if ($command->t1)
            $schedule->setT1(new \DateTime($command->t1));
        if ($command->s2)
            $schedule->setS2(new \DateTime($command->s2));
        if ($command->t2)
            $schedule->setT2(new \DateTime($command->t2));
        if ($command->s3)
            $schedule->setS3(new \DateTime($command->s3));
        if ($command->t3)
            $schedule->setT3(new \DateTime($command->t3));
        if ($command->s4)
            $schedule->setS4(new \DateTime($command->s4));
        if ($command->t4)
            $schedule->setT4(new \DateTime($command->t4));
        if ($command->s5)
            $schedule->setS5(new \DateTime($command->s5));
        if ($command->t5)
            $schedule->setT5(new \DateTime($command->t5));
        if ($command->s6)
            $schedule->setS6(new \DateTime($command->s6));
        if ($command->t6)
            $schedule->setT6(new \DateTime($command->t6));
        if ($command->s7)
            $schedule->setS7(new \DateTime($command->s7));
        if ($command->t7)
            $schedule->setT7(new \DateTime($command->t7));

        $em->persist($schedule);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $schedule->getId());
    }
}