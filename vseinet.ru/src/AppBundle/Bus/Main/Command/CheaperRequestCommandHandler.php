<?php 

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\CheaperRequest;

class CheaperRequestCommandHandler extends MessageHandler
{
    public function handle(CheaperRequestCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $request = new CheaperRequest();
        $request->setBaseProductId($command->product->getId());

        if (null !== $command->userData->userId) {
            $request->setUserId($command->userData->userId);
            // @todo: save contactdIds
        } else {
            $request->setComuserId($command->userData->comuserId);
        }

        $request->setGeoCityId($command->geoCityId);
        $request->setCompetitorPrice($command->competitorPrice);
        $request->setCompetitorLink($command->competitorLink);
        $request->setComment($command->comment);

        $em->persist($request);
        $em->flush();
    }
}
