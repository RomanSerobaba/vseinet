<?php 

namespace OrgBundle\Bus\Employee\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Employee;
use OrgBundle\Entity\EmployeeToDepartment;
use OrgBundle\Entity\EmploymentHistory;
use OrgBundle\Repository\EmployeeRepository;

class SetFiredAtCommandHandler extends MessageHandler
{
    public function handle(SetFiredAtCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /** @var EmployeeRepository $employeeRep */
        $employeeRep = $em->getRepository(Employee::class);

        /** @var Employee $employee */
        $employee = $employeeRep->find($command->id);
        if (!$employee)
            throw new EntityNotFoundException('Employee not found');


        /** @var EmploymentHistory[] $empHist */
        $empHist = $em->createQuery('
                SELECT eh
                FROM OrgBundle:EmploymentHistory AS eh
                WHERE
                    eh.employeeUserId = :user_id
                    AND (eh.firedAt IS NULL OR eh.firedAt >= CURRENT_TIMESTAMP())
            ')
            ->setParameter('user_id', $employee->getUserId())
            ->getResult();

        if (count($empHist) < 1)
            throw new EntityNotFoundException('Employment history not found');

        /** @var EmployeeToDepartment[] $empToDep */
        $empToDep = $em->createQuery('
                SELECT ed
                FROM OrgBundle:EmployeeToDepartment AS ed
                WHERE
                    ed.employeeUserId = :user_id
                    AND (ed.activeSince IS NULL OR ed.activeSince <= CURRENT_TIMESTAMP())
                    AND (ed.activeTill IS NULL OR ed.activeTill >= CURRENT_TIMESTAMP())
            ')
            ->setParameter('user_id', $employee->getUserId())
            ->getResult();

        if (count($empToDep) < 1)
            throw new EntityNotFoundException('Employee in department not found');


        foreach ($empHist as $eh) {
            $eh->setFiredAt(new \DateTime($command->value . ' 23:59:59'));
        }

        foreach ($empToDep as $ed) {
            $ed->setActiveTill(new \DateTime($command->value . ' 23:59:59'));
        }

        $em->flush();
    }
}