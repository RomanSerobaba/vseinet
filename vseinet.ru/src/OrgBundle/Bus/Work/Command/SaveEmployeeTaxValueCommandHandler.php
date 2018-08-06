<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Components\EmployeeComponent;
use OrgBundle\Entity\Salary;

class SaveEmployeeTaxValueCommandHandler extends MessageHandler
{
    /**
     * @param SaveEmployeeTaxValueCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(SaveEmployeeTaxValueCommand $command)
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

        $salary->setTax($command->value);

        $em->persist($salary);
        $em->flush();
    }
}