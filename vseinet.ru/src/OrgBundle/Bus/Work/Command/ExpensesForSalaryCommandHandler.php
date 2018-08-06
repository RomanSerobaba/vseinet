<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Bus\Work\Command\Schema\EmployeePayment;
use OrgBundle\Entity\Salary;

class ExpensesForSalaryCommandHandler extends MessageHandler
{
    /**
     * @param ExpensesForSalaryCommand $command
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(ExpensesForSalaryCommand $command)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var EmployeePayment $payment */
        foreach ($command->payments as $payment) {
            /** @var Salary $salary */
            $salary = $em->getRepository(Salary::class)
                ->findOneBy([
                    'employeeId' => $payment->employeeUserId,
                    'date' => $payment->date
                ]);

            if ($salary) {
                $amount = $salary->getQueueAmount();
                if ($amount) {
                    // TODO: Добавить выдаваемую сумму в расходы!

                    $salary->setPaid(($salary->getPaid() ?? 0) + $amount);
                    $salary->setQueueAmount(null);
                    $em->persist($salary);
                }
            }
        }
        $em->flush();
    }
}