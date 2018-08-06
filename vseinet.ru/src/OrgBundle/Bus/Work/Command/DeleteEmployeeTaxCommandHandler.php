<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\EmployeeComponent;
use OrgBundle\Entity\EmployeeTax;

class DeleteEmployeeTaxCommandHandler extends MessageHandler
{
    /**
     * @param DeleteEmployeeTaxCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(DeleteEmployeeTaxCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $employeeComponent = new EmployeeComponent($em);
        $employee = $employeeComponent->getInfo($command->id, false);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');

        $command->activeSince = date('Y-m-01', ($command->activeSince ? strtotime($command->activeSince) : null));
        $beforeSince = date('Y-m-d', strtotime($command->activeSince . ' - 1 days'));

        if ($command->activeTill && ($command->activeTill < $command->activeSince))
            $command->activeTill = $command->activeSince;

        $afterTill = null;
        if ($command->activeTill) {
            $command->activeTill = date('Y-m-d', strtotime('last day of this month ' . $command->activeTill));
            $afterTill = date('Y-m-d', strtotime($command->activeTill . ' + 1 days'));
        }

        $qText = '
            SELECT et
            FROM OrgBundle:EmployeeTax AS et
            WHERE et.employeeUserId = :userId
                AND (et.activeTill >= :since OR et.activeTill IS NULL)';
        $qParam = [
            'userId' => $employee->userId,
            'since' => $beforeSince
        ];
        if ($command->activeTill) {
            $qText .= '
                AND et.activeSince <= :till';
            $qParam['till'] = $afterTill;
        }
        $qText .= '
            ORDER BY et.activeSince';

        /** @var EmployeeTax[] $taxes */
        $taxes = $em->createQuery($qText)
            ->setParameters($qParam)
            ->getResult();

        /** @var EmployeeTax|null $tax */
        $tax = null;
        foreach ($taxes as $item) {
            $since = $item->getActiveSince()->format('Y-m-d');
            $till = $item->getActiveTill() ? $item->getActiveTill()->format('Y-m-d') : null;

            // Время действия этого элемента заканчивается до начала нового?
            if ($till && ($till <= $beforeSince))
                continue;

            if (!$tax) {
                if ($since >= $command->activeSince) {
                    if ($afterTill && (!$till || $till >= $afterTill)) {
                        $tax = $item;
                        $tax->setActiveSince(new \DateTime($afterTill));
                        $em->persist($tax);
                    }
                } elseif ($since < $command->activeSince) {
                    if ($afterTill && (!$till || $till >= $afterTill)) {
                        $tax = new EmployeeTax();
                        $tax->setEmployeeUserId($item->getEmployeeUserId());
                        $tax->setActiveSince(new \DateTime($afterTill));
                        $tax->setActiveTill($item->getActiveTill());
                        $em->persist($tax);
                    }

                    $item->setActiveTill(new \DateTime($beforeSince));
                    $tax = $item;
                    $em->persist($item);
                }
            }

            if ($item !== $tax)
                $em->remove($item);
        }
        $em->flush();
    }
}