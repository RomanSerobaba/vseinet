<?php

namespace AppBundle\Bus\ResourceMethod\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Resource;
use AppBundle\Entity\ResourceMethod;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $method = $em->getRepository(ResourceMethod::class)->find($command->id);
        if (!$method instanceof ResourceMethod) {
            throw new NotFoundHttpException(sprintf('Метод ресурса %d не найден', $command->id));
        }

        if ($method->getResourceId() != $command->resourceId) {
            $resource = $em->getRepository(Resource::class)->find($command->resourceId);
            if (!$resource instanceof Resource) {
                throw new NotFoundHttpException(sprintf('Ресурс %d не найден', $command->resourceId));
            }
            $method->setResourceId($resource->getId());
        }

        if ($method->getApiMethodId() != $command->apiMethodId) {
            $apiMethod = $em->getRepository(ApiMethod::class)->find($command->apiMethodId);
            if (!$apiMethod instanceof ApiMethod) {
                throw new NotFoundHttpException(sprintf('Метод API %d не найден', $command->apiMethodId));
            }
            $method->setApiMethodId($apiMethod->getId());
        }

        $em->persist($method);
        $em->flush();
    }
}