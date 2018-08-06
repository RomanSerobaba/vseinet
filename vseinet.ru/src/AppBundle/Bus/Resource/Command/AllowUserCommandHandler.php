<?php

namespace AppBundle\Bus\Resource\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\User;
use AppBundle\Entity\Resource;
use AppBundle\Entity\ResourceUserCodex;

class AllowUserCommandHandler extends MessageHandler
{
    public function handle(AllowUserCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($command->userId);
        if (!$user instanceof User) {
            throw new NotFoundHttpException(sprintf('Пользователь %d не найден', $command->userId));
        }

        $resource = $em->getRepository(Resource::class)->find($command->resourceId);
        if (!$resource instanceof Resource) {
            throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $command->resourceId));
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 1
            FROM AppBundle:ResourceSubroleCodex rsrc 
            INNER JOIN AppBundle:UserToSubrole u2sr WITH u2sr.subroleId = rsrc.subroleId
            WHERE u2sr.userId = :userId AND rsrc.resourceId = :resourceId
        ");
        $q->setParameter('userId', $user->getId());
        $q->setParameter('resourceId', $resource->getId());
        $q->setMaxResults(1);
        $inherit = $q->getOneOrNullResult();

        $codex = $em->getRepository(ResourceUserCodex::class)->findOneBy([
            'userId' => $user->getId(),
            'resourceId' => $resource->getId(),
        ]);

        if ($inherit) {
            if ($codex instanceof ResourceUserCodex) {
                $em->remove($codex);
            }
        }
        else {
            if (!$codex instanceof ResourceUserCodex) {
                $codex = new ResourceUserCodex();
                $codex->setUserId($user->getId());
                $codex->setResourceId($resource->getId());
            }
            $codex->setIsAllowed(true);
            $em->persist($codex);
        }
        $em->flush();
    }
}