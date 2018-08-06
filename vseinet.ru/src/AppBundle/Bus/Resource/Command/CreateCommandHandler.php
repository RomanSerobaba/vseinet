<?php

namespace AppBundle\Bus\Resource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ResourceGroup;
use AppBundle\Entity\Resource;
use Doctrine\ORM\NoResultException;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(ResourceGroup::class)->find($command->groupId);
        if (!$group instanceof ResourceGroup) {
            throw new NotFoundHttpException(sprintf('Группа ресурсов %d не найдена', $command->groupId));
        }

        $q = $em->createQuery("
            SELECT MAX(r.sortOrder)
            FROM AppBundle:Resource r 
            WHERE r.groupId = :groupId 
        ");
        $q->setParameter('groupId', $group->getId());
        try {
            $sortOrder = $q->getSingleScalarResult() + 1;
        }
        catch (NoResultException $e) {
            $sortOrder = 1;
        }

        $resource = new Resource();
        $resource->setName($command->name);
        $resource->setGroupId($group->getId());
        $resource->setPath($command->path);
        $resource->setCode(Specification\CodeBuilder::build($command->path));
        $resource->setDescription($command->description);
        $resource->setSortOrder($sortOrder);
        $resource->setIsMenu($command->isMenu);

        $em->persist($resource);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $resource->getId());
    }
}