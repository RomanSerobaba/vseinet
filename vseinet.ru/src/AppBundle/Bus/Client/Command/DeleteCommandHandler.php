<?php

namespace AppBundle\Bus\Client\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Client;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $clientManager = $this->get('fos_oauth_server.client_manager.default');

        $client = $clientManager->findClientBy(['id' => $command->id]);
        if (!$client instanceof Client) {
            throw new NotFoundHttpException(sprintf('Клиент API %d не найден', $command->id));
        }
        $clientManager->deleteClient($client);
    }
}