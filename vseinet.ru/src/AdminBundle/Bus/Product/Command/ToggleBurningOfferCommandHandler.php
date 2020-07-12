<?php

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\ProductTotalSale;
use AppBundle\Enum\UserRole;
use Classes\Exceptions\BadRequestHttpException;

class ToggleBurningOfferCommandHandler extends MessageHandler
{
    public function handle(ToggleBurningOfferCommand $command)
    {
        if (!$this->getUser()->isRoleIn([UserRole::ADMIN])) {
            throw new BadRequestHttpException(sprintf('У вас нет прав, обратитесь к уполномоченному'));
        }

        $em = $this->getDoctrine()->getManager();
        $burningOffer = $em->getRepository(ProductTotalSale::class)->find($command->baseProductId);
        if ($command->value && !$burningOffer) {
            $burningOffer = new ProductTotalSale();
            $burningOffer->setBaseProductId($command->baseProductId);
            $em->persist($burningOffer);
            $em->flush();
        } elseif (!$command->value && $burningOffer) {
            $em->remove($burningOffer);
            $em->flush();
        }
    }
}
