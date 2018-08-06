<?php

namespace AppBundle\Bus\ResourceMethod\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\User;
use AppBundle\Entity\ResourceMethod;
use AppBundle\Entity\ApiMethod;
use AppBundle\Entity\ApiMethodUserCodex;

class DenyUserCommandHandler extends MessageHandler
{
    public function handle(DenyUserCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->find($command->userId);
        if (!$user instanceof User) {
            throw new NotFoundHttpException(sprintf('Пользователь %d не найден', $command->userId));
        }

        $method = $em->getRepository(ResourceMethod::class)->find($command->methodId);
        if (!$method instanceof ResourceMethod) {
            throw new NotFoundHttpException(sprintf('Метод ресурса %d не найден', $command->methodId));
        }
        $api = $em->getRepository(ApiMethod::class)->find($method->getApiMethodId());
        if (!$api instanceof ApiMethod) {
            throw new NotFoundHttpException(sprintf('Метод API %d не найден', $method->getApiMethodId()));
        }

        $q = $em->createQuery("
            SELECT 1
            FROM AppBundle:ApiMethodSubroleCodex amsrc
            INNER JOIN AppBundle:UserToSubrole u2sr WITH u2sr.subroleId = amsrc.subroleId 
            WHERE u2sr.userId = :userId AND amsrc.apiMethodId = :apiMethodId 
        ");
        $q->setParameter('userId', $user->getId());
        $q->setParameter('apiMethodId', $method->getApiMethodId());
        $q->setMaxResults(1);
        $inherit = $q->getOneOrNullResult();

        $codex = $em->getRepository(ApiMethodUserCodex::class)->findOneBy([
            'userId' => $user->getId(),
            'apiMethodId' => $method->getApiMethodId(),
        ]);

        if ($inherit) {
            if (!$codex instanceof ApiMethodUserCodex) {
                $codex = new ApiMethodUserCodex();
                $codex->setUserId($user->getId());
                $codex->setApiMethodId($method->getApiMethodId());
            }
            $codex->setIsAllowed(false);
            $em->persist($codex);
        }
        elseif ($codex instanceof ApiMethodUserCodex) {
            $em->remove($codex);
        }
        $em->flush();
    }
}