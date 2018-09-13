<?php 

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Bus\Exception\ValidationException;
use AppBundle\Entity\CheaperRequest;

class CheaperRequestCommandHandler extends MessageHandler
{
    public function handle(CheaperRequestCommand $command)
    {
        if (!$this->urlIsValid($command)) {
            throw new ValidationException([
                'competitorLink' => 'С указанного сайта заявки не принимаются',
            ]);
        }

        $em = $this->getDoctrine()->getManager();

        $request = new CheaperRequest();
        $request->setBaseProductId($command->product->getId());

        if (null !== $command->userData->userId) {
            $request->setUserId($command->userData->userId);
        } else {
            $request->setComuserId($command->userData->comuserId);
        }

        $request->setGeoCityId($command->geoCityId);
        $request->setCompetitorPrice($command->competitorPrice);
        $request->setCompetitorLink($command->competitorLink);
        $request->setText($command->text);

        $em->persist($request);
        $em->flush();
    }

    protected function urlIsValid($command)
    {
        $urlFragments = parse_url($command->competitorLink);
        if (empty($urlFragments['host'])) {
            if (empty($urlFragments['path'])) {
                return false;
            }
            $urlFragments['host'] = $urlFragments['path'];
        }

        foreach ($command->competitors as $competitor) {
            $competitorUrlFragments = parse_url($competitor->getLink());
            if (empty($competitorUrlFragments['host'])) {
                continue;
            }
            if ($urlFragments['host'] == $competitorUrlFragments['host']) {
                return true;
            }
        }

        return false;
    }
}
