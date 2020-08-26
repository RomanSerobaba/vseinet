<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Exception\ValidationException;
use AppBundle\Entity\CheaperRequest;
use AppBundle\Entity\Competitor;

class CheaperRequestCommandHandler extends MessageHandler
{
    public function handle(CheaperRequestCommand $command)
    {
        if (!$this->urlIsValid($command)) {
            throw new ValidationException('competitorLink', 'С указанного сайта заявки не принимаются');
        }

        $em = $this->getDoctrine()->getManager();

        $request = new CheaperRequest();
        $request->setBaseProductId($command->baseProductId);

        if (null !== $command->userData->userId) {
            $request->setUserId($command->userData->userId);
        } else {
            $request->setComuserId($command->userData->comuserId);
        }

        $request->setGeoCityId($command->geoCityId);
        $request->setCompetitorPrice($command->competitorPrice);
        $request->setCompetitorLink($command->competitorLink);
        $request->setComment($command->comment);
        $request->setCreatedAt(new \DateTime());

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
        $urlFragments['host'] = preg_replace('~^www\.~isu', '', $urlFragments['host']);

        $competitors = $this->getDoctrine()->getManager()->getRepository(Competitor::class)->findBy(['isActive' => true, 'channel' => ['site', 'pricelist']]);
        foreach ($competitors as $competitor) {
            $competitorUrlFragments = parse_url($competitor->getLink());
            if (empty($competitorUrlFragments['host'])) {
                continue;
            }
            $host = implode('.', array_slice(explode('.', $competitorUrlFragments['host']), -2, 2));

            if (false !== strstr(strtolower($urlFragments['host']), strtolower($host))) {
                return true;
            }
        }

        return false;
    }
}
