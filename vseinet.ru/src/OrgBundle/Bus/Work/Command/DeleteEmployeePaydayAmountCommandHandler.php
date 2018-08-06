<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\EmployeeComponent;
use OrgBundle\Entity\Salary;

class DeleteEmployeePaydayAmountCommandHandler extends MessageHandler
{
    /**
     * @param DeleteEmployeePaydayAmountCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(DeleteEmployeePaydayAmountCommand $command)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $employeeComponent = new EmployeeComponent($em);
        $employee = $employeeComponent->getInfo($command->id, false);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');

        $command->date = date('Y-m-01', $command->date ? strtotime($command->date) : time());

        /** @var Salary $salary */
        $salary = $em->getRepository(Salary::class)
            ->findOneBy([
                'employeeId' => $employee->userId,
                'date' => new \DateTime($command->date)
            ]);

        if (!$salary) {
            $salary = new Salary();
            $salary->setEmployeeId($employee->userId);
            $salary->setDate(new \DateTime($command->date));
        } elseif ($salary->getQueueAmount()) {
            $salary->setQueueAmount(null);
        }

        $em->persist($salary);
        $em->flush();
    }
}