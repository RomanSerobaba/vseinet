<?php

namespace AppBundle\Bus\ResourceGroup\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Client;
use AppBundle\Entity\ResourceGroup;
use Doctrine\ORM\NoResultException;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $client = $em->getRepository(Client::class)->find($command->clientId);
        if (!$client instanceof Client) {
            throw new NotFoundHttpException(sprintf('Клиент API %d не найден', $command->clientId));
        }

        $q = $em->createQuery("
            SELECT MAX(rg.sortOrder)
            FROM AppBundle:ResourceGroup rg 
            WHERE rg.clientId = :clientId 
        ");
        $q->setParameter('clientId', $client->getId());
        try {
            $sortOrder = $q->getSingleScalarResult() + 1;
        }
        catch (NoResultException $e) {
            $sortOrder = 1;
        }

        $group = new ResourceGroup();
        $group->setName($command->name);
        $group->setClientId($client->getId());
        $group->setSortOrder($sortOrder);

        $em->persist($group);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $group->getId());
    }
}