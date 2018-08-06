<?php

namespace AppBundle\Bus\Resource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadrequestHttpException;
use AppBundle\Entity\Resource;

class SortCommandHandler extends MessageHandler
{
    public function handle(SortCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $resource = $em->getRepository(Resource::class)->find($command->id);
        if (!$resource instanceof Resource) {
            throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $command->id));
        }

        if (0 === $command->underId) {
            $sortOrder = 0;
        }
        else {
            $under = $em->getRepository(Resource::class)->find($command->underId);
            if (!$under instanceof Resource) {
                throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $command->underId));
            }
            if ($resource->getGroupId() != $under->getGroupId()) {
                throw new BadrequestHttpException('Сортировка ресурсов возможна только в пределах одной группы');
            }
            $sortOrder = $under->getSortOrder();
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            UPDATE AppBundle:Resource r  
            SET r.sortOrder = r.sortOrder + 1
            WHERE r.groupId = :groupId AND r.sortOrder > :sortOrder 
        ");
        $q->setParameter('groupId', $resource->getGroupId());
        $q->setParameter('sortOrder', $sortOrder);
        $q->execute();

        $resource->setSortOrder($sortOrder + 1);

        $em->persist($resource);
        $em->flush();
    }
}