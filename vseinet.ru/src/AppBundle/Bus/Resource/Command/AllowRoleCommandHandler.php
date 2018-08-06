<?php

namespace AppBundle\Bus\Resource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Subrole;
use AppBundle\Entity\Resource;
use AppBundle\Entity\ResourceSubroleCodex;

class AllowRoleCommandHandler extends MessageHandler
{
    public function handle(AllowRoleCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $subrole = $em->getRepository(Subrole::class)->find($command->subroleId);
        if (!$subrole instanceof Subrole) {
            throw new NotFoundHttpException(sprintf('Роль %d не найдена', $command->subroleId));
        }

        $resource = $em->getRepository(Resource::class)->find($command->resourceId);
        if (!$resource instanceof Resource) {
            throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $command->resourceId));
        }

        $codex = $em->getRepository(ResourceSubroleCodex::class)->findBy([
            'subroleId' => $subrole->getId(),
            'resourceId' => $resource->getId(),
        ]);
        if (!$codex instanceof ResourceSubroleCodex) {
            $codex = new ResourceSubroleCodex();
            $codex->setSubroleId($subrole->getId());
            $codex->setResourceId($resource->getId());
            $em->persist($codex);
            $em->flush();
        }
    }
}