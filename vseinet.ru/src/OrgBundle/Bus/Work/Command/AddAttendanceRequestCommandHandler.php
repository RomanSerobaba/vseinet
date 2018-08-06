<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Employee;
use OrgBundle\Entity\EmployeeCancelRequest;
use OrgBundle\Entity\EmployeeFine;

class AddAttendanceRequestCommandHandler extends MessageHandler
{
    /**
     * @param AddAttendanceRequestCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(AddAttendanceRequestCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        /** @var Employee $employee */
        $employee = $em->getRepository(Employee::class)->find($command->id);

        if (!$employee)
            throw new EntityNotFoundException('Сотрудник не найден');

        if (!$command->date)
            $command->date = date('Y-m-d');

        /** @var EmployeeFine $fine */
        $fine = $em->getRepository(EmployeeFine::class)
                ->findOneBy([
                    'employeeId' => $command->id,
                    'type' => $command->type,
                    'date' => ($command->date instanceof \DateTime) ? $command->date : new \DateTime($command->date)
                ]);

        if ($command->type == EmployeeFine::TYPE_OVERTIME) {

            if (!$fine) {
                $fine = new EmployeeFine();
                $fine->setEmployeeId($employee->getUserId());
                $fine->setType($command->type);
                $fine->setDate(new \DateTime($command->date));

                $fine->setStatus(EmployeeFine::STATUS_CREATED);
                $fine->setStatusChangedBy($currentUser->getId());
                $fine->setStatusChangedAt(new \DateTime());
            }

            $fine->setTime(new \DateTime($command->time));
            $fine->setCause($command->cause);

            $em->persist($fine);

        } elseif ($fine) {

            /** @var EmployeeCancelRequest $cancelRequest */
            $cancelRequest = $em->getRepository(EmployeeCancelRequest::class)->find($fine->getId());

            if (!$cancelRequest) {
                $cancelRequest = new EmployeeCancelRequest();
                $cancelRequest->setFine($fine);

                $cancelRequest->setStatus(EmployeeFine::STATUS_CREATED);
                $cancelRequest->setStatusChangedBy($currentUser->getId());
                $cancelRequest->setStatusChangedAt(new \DateTime());
            }

            $cancelRequest->setCause($command->cause);

            $em->persist($cancelRequest);
        }

        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $fine->getId());
    }
}