<?php

namespace AppBundle\Bus\ResourceMethod\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Resource;
use AppBundle\Entity\ResourceMethod;
use AppBundle\Entity\ApiMethod;
use Doctrine\ORM\NoResultException;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $resource = $em->getRepository(Resource::class)->find($command->resourceId);
        if (!$resource instanceof Resource) {
            throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $command->resourceId));
        }

        $apiMethod = $em->getRepository(ApiMethod::class)->find($command->apiMethodId);
        if (!$apiMethod instanceof ApiMethod) {
            throw new NotFoundHttpException(sprintf('Метод API %d не найден', $command->apiMethodId));
        }

        $method = new ResourceMethod();
        $method->setResourceId($resource->getId());
        $method->setApiMethodId($apiMethod->getId());

        $em->persist($method);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $method->getId());
    }
}