<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\ClientSuggestion;

class ClientSuggestionCommandHandler extends MessageHandler
{
    public function handle(ClientSuggestionCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $suggestion = new ClientSuggestion();
        $suggestion->setText($command->text);
        if (null !== $command->userData->userId) {
            $suggestion->setUserId($command->userData->userId);
        } else {
            $suggestion->setComuserId($command->userData->comuserId);
        }

        $em->persist($suggestion);
        $em->flush();
    }
}
