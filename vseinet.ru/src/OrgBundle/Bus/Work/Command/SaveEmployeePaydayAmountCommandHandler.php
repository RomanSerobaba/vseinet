<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\EmployeeComponent;
use OrgBundle\Entity\Salary;

class SaveEmployeePaydayAmountCommandHandler extends MessageHandler
{
    /**
     * @param SaveEmployeePaydayAmountCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     */
    public function handle(SaveEmployeePaydayAmountCommand $command)
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
        }

        $salary->setQueueAmount(($salary->getQueueAmount() ?? 0) + $command->amount);

        $em->persist($salary);
        $em->flush();
    }
}