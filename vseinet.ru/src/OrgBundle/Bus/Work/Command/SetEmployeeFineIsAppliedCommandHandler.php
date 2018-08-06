<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\EmployeeCancelRequest;
use OrgBundle\Entity\EmployeeFine;

class SetEmployeeFineIsAppliedCommandHandler extends MessageHandler
{
    /**
     * @param SetEmployeeFineIsAppliedCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(SetEmployeeFineIsAppliedCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        /** @var EmployeeFine $fine */
        $fine = $em->getRepository(EmployeeFine::class)->find($command->id);

        if (!$fine)
            throw new EntityNotFoundException('(Де)премирование не найдено');

        if ($fine->getType() && in_array($fine->getType(), [EmployeeFine::TYPE_ABSENCE, EmployeeFine::TYPE_UNWORKING])) {

            /** @var EmployeeCancelRequest $cancelRequest */
            $cancelRequest = $fine->getCancelRequest();

            if (!$cancelRequest) {
                $cancelRequest = new EmployeeCancelRequest();

                $cancelRequest->setFine($fine);
                $cancelRequest->setCause('Отмена нарушения');
                $cancelRequest->setStatus(EmployeeFine::STATUS_CREATED);
            }

            if ($command->value) {
                if ($cancelRequest->getStatus() != EmployeeFine::STATUS_APPLIED) {
                    $cancelRequest->setStatus(EmployeeFine::STATUS_APPLIED);
                    $cancelRequest->setStatusChangedBy($currentUser->getId());
                    $cancelRequest->setStatusChangedAt(new \DateTime());

                    if (!$cancelRequest->getApprovedAt()) {
                        $cancelRequest->setApprovedBy($currentUser->getId());
                        $cancelRequest->setApprovedAt(new \DateTime());
                    }
                }
            } elseif ($cancelRequest->getStatus() == EmployeeFine::STATUS_APPLIED) {
                $cancelRequest->setStatus(EmployeeFine::STATUS_CREATED);
                $cancelRequest->setStatusChangedBy($currentUser->getId());
                $cancelRequest->setStatusChangedAt(new \DateTime());
            }

            $em->persist($cancelRequest);

        } else {
            if ($command->value) {
                if ($fine->getStatus() != EmployeeFine::STATUS_APPLIED) {
                    $fine->setStatus(EmployeeFine::STATUS_APPLIED);
                    $fine->setStatusChangedBy($currentUser->getId());
                    $fine->setStatusChangedAt(new \DateTime());

                    if (!$fine->getApprovedAt()) {
                        $fine->setApprovedBy($currentUser->getId());
                        $fine->setApprovedAt(new \DateTime());
                    }
                }
            } elseif ($fine->getStatus() == EmployeeFine::STATUS_APPLIED) {
                $fine->setStatus(EmployeeFine::STATUS_CREATED);
                $fine->setStatusChangedBy($currentUser->getId());
                $fine->setStatusChangedAt(new \DateTime());
            }
        }

        $em->persist($fine);
        $em->flush();
    }
}