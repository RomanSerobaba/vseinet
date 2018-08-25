<?php 

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Suggestion;

class SuggestionCommandHandler extends MessageHandler
{
    public function handle(SuggestionCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $suggestion = new Suggestion();
        $suggestion->setText($command->text);
        $suggestion->setCreatedAt(new \DateTime());
        if (null !== $command->userData->userId) {
            $suggestion->setUserId($command->userData->userId);
            // @todo: save contactIds
        } else {
            $suggestion->setComuserId($command->userData->comuserId);
        }
        $suggestion->setIsChecked(false);
        
        $em->persist($suggestion);
        $em->flush();
    }
}
