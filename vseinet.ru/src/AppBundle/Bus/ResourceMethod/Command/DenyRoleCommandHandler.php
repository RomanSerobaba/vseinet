<?php

namespace AppBundle\Bus\ResourceMethod\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use AppBundle\Entity\Subrole;
use AppBundle\Entity\ResourceMethod;
use AppBundle\Entity\ApiMethod;
use AppBundle\Entity\ApiMethodSubroleCodex;

class DenyRoleCommandHandler extends MessageHandler
{
    public function handle(DenyRoleCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $subrole = $em->getRepository(Subrole::class)->find($command->subroleId);
        if (!$subrole instanceof Subrole) {
            throw new NotFoundHttpException(sprintf('Роль %d не найдена', $command->subroleId));
        }

        $method = $em->getRepository(ResourceMethod::class)->find($command->methodId);
        if (!$method instanceof ResourceMethod) {
            throw new NotFoundHttpException(sprintf('Метод ресурса %d не найден', $command->methodId));
        }
        $api = $em->getRepository(ApiMethod::class)->find($method->getApiMethodId());
        if (!$api instanceof ApiMethod) {
            throw new NotFoundHttpException(sprintf('Метод API %d не найден', $method->getApiMethodId()));
        }

        $codex = $em->getRepository(ApiMethodSubroleCodex::class)->findOneBy([
            'subroleId' => $subrole->getId(),
            'apiMethodId' => $method->getApiMethodId(),
        ]);
        if ($codex instanceof ApiMethodSubroleCodex) {
            $em->remove($codex);
            $em->flush();
        }
    }
}