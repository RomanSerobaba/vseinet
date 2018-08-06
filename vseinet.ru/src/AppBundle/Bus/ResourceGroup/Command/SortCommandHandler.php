<?php

namespace AppBundle\Bus\ResourceGroup\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadrequestHttpException;
use AppBundle\Entity\ResourceGroup;

class SortCommandHandler extends MessageHandler
{
    public function handle(SortCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(ResourceGroup::class)->find($command->id);
        if (!$group instanceof ResourceGroup) {
            throw new NotFoundHttpException(sprintf('Группа ресурсов %d не найдена', $command->id));
        }

        if (0 === $command->underId) {
            $sortOrder = 0;
        }
        else {
            $under = $em->getRepository(ResourceGroup::class)->find($command->underId);
            if (!$under instanceof ResourceGroup) {
                throw new NotFoundHttpException(sprintf('Группа ресурсов %d не найдена', $command->underId));
            }
            if ($group->getClientId() != $under->getClientId()) {
                throw new BadrequestHttpException('Сортировка групп ресурсов возможна только в пределах одного клиента');
            }
            $sortOrder = $under->getSortOrder();
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            UPDATE AppBundle:ResourceGroup rg  
            SET rg.sortOrder = rm.sortOrder + 1
            WHERE rg.clientId = :clientId AND rg.sortOrder > :sortOrder 
        ");
        $q->setParameter('clientId', $group->getClientId());
        $q->setParameter('sortOrder', $sortOrder);
        $q->execute();

        $group->setSortOrder($sortOrder + 1);

        $em->persist($group);
        $em->flush();
    }
}