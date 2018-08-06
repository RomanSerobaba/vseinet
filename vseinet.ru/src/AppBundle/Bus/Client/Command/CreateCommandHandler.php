<?php

namespace AppBundle\Bus\Client\Command;

use AppBundle\Bus\Message\MessageHandler;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $clientManager = $this->get('fos_oauth_server.client_manager.default');

        $client = $clientManager->createClient();
        $client->setName($command->name);
        $client->setRedirectUris([$command->redirectUri]);
        $client->setAllowedGrantTypes(['password', 'refresh_token']);
        $clientManager->updateClient($client);

        $this->get('uuid.manager')->saveId($command->uuid, $client->getId());
    }
}