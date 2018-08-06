<?php

namespace AppBundle\Bus\Resource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ResourceGroup;
use AppBundle\Entity\Resource;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $resource = $em->getRepository(Resource::class)->find($command->id);
        if (!$resource instanceof Resource) {
            throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $command->id));
        }

        // if ($resource->getGroupId() != $command->groupId) {
        //     $group = $em->getRepository(ResourceGroup::class)->find($command->groupId);
        //     if (!$group instanceof ResourceGroup) {
        //         throw new NotFoundHttpException(sprintf('Группа ресурсов %d не найдена', $command->groupId));
        //     }
        //     $resource->setGroupId($group->getId());
        // }

        $resource->setName($command->name);
        $resource->setPath($command->path);
        $resource->setCode(Specification\CodeBuilder::build($command->path));
        $resource->setDescription($command->description);
        $resource->setIsMenu($command->isMenu);

        $em->persist($resource);
        $em->flush();
    }
}