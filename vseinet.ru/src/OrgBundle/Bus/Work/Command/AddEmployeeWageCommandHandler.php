<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\EmployeeComponent;
use OrgBundle\Entity\EmployeeWage;

class AddEmployeeWageCommandHandler extends MessageHandler
{
    /**
     * @param AddEmployeeWageCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(AddEmployeeWageCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $employeeComponent = new EmployeeComponent($em);
        $employee = $employeeComponent->getInfo($command->id, false);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');

        if (!$command->activeSince)
            $command->activeSince = date('Y-m-d');

        if ($command->activeTill && ($command->activeTill < $command->activeSince))
            $command->activeTill = $command->activeSince;

        $qText = '
            SELECT ew
            FROM OrgBundle:EmployeeWage AS ew
            WHERE ew.employeeUserId = :userId
                AND (ew.activeTill >= :since OR ew.activeTill IS NULL)';
        $qParam = [
            'userId' => $employee->userId,
            'since' => $command->activeSince
        ];
        if ($command->activeTill) {
            $qText .= '
                AND ew.activeSince <= :till';
            $qParam['till'] = $command->activeTill;
        }
        $qText .= '
            ORDER BY ew.activeSince';

        /** @var EmployeeWage[] $wages */
        $wages = $em->createQuery($qText)
            ->setParameters($qParam)
            ->getResult();

        $wage = null;
        foreach ($wages as $item) {
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
                    $itemNew = new EmployeeWage();
                    $itemNew->setEmployeeUserId($employee->userId);

                    $tm = new \DateTime();
                    $tm->setTimestamp(strtotime($command->activeTill . ' + 1 days'));
                    $itemNew->setActiveSince($tm);
                    $itemNew->setActiveTill($item->getActiveTill());

                    $itemNew->setPlanFunction($item->getPlanFunction());
                    $itemNew->setConstantBase($item->getConstantBase());
                    $itemNew->setPlanBase($item->getPlanBase());

                    $em->persist($itemNew);
                }

                // Устанавливаем конец старого перед началом нового
                $tm = new \DateTime();
                $tm->setTimestamp(strtotime($command->activeSince . ' - 1 days'));
                $item->setActiveTill($tm);

                $em->persist($item);

                // Старый отрезок внутри нового?
            } elseif (!$command->activeTill || ($till && ($till <= $command->activeTill))) {
                if (!$wage) {
                    // Первый из внутренних отрезков используем для нового расписания
                    $wage = $item;

                    $wage->setActiveSince(new \DateTime($command->activeSince));
                    if ($command->activeTill)
                        $wage->setActiveTill(new \DateTime($command->activeTill));

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

        if (!$wage) {
            $wage = new EmployeeWage();
            $wage->setEmployeeUserId($employee->userId);

            $wage->setActiveSince(new \DateTime($command->activeSince));
            if ($command->activeTill)
                $wage->setActiveTill(new \DateTime($command->activeTill));

            $wage->setPlanFunction(EmployeeWage::PLAN_SOFT);
        }

        $wage->setConstantBase($command->constantBase);
        $wage->setPlanBase($command->planBase);

        $em->persist($wage);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $wage->getId());
    }
}