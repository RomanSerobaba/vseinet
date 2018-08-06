<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\EmployeeComponent;
use OrgBundle\Entity\EmployeeTax;

class AddEmployeeTaxCommandHandler extends MessageHandler
{
    /**
     * @param AddEmployeeTaxCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(AddEmployeeTaxCommand $command)
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
        $lastDate = $command->activeTill;
        foreach ($taxes as $item) {
            $since = $item->getActiveSince()->format('Y-m-d');
            $till = $item->getActiveTill() ? $item->getActiveTill()->format('Y-m-d') : null;

            // Время действия этого элемента заканчивается до начала нового?
            if ($till && ($till <= $beforeSince))
                continue;

            if (!$tax) {
                $tax = $item;

                if ($since > $command->activeSince) {
                    $tax->setActiveSince(new \DateTime($command->activeSince));
                }

                if (!$lastDate) {
                    $tax->setActiveTill(null);
                } elseif (!$till || ($till > $lastDate)) {
                    $lastDate = $till;
                } elseif ($till < $lastDate) {
                    $tax->setActiveTill(new \DateTime($lastDate));
                }

                $em->persist($tax);

            } else {

                if ($lastDate && (!$till || ($till > $lastDate))) {
                    $lastDate = $till;
                    $tax->setActiveTill($lastDate ? new \DateTime($lastDate) : null);
                }

                $em->remove($item);
            }
        }

        if (!$tax) {
            $tax = new EmployeeTax();
            $tax->setEmployeeUserId($employee->userId);

            $tax->setActiveSince(new \DateTime($command->activeSince));
            if ($command->activeTill)
                $tax->setActiveTill(new \DateTime($command->activeTill));

            $em->persist($tax);
        }

        $em->persist($tax);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $tax->getId());
    }
}