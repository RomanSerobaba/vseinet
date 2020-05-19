<?php

namespace AdminBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\PartnerProduct;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RestoreCommandHandler extends MessageHandler
{
    public function handle(RestoreCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->baseProductId));
        }

        $partnerProduct = $em->getRepository(PartnerProduct::class)->find($command->partnerProductId);
        if (!$partnerProduct instanceof PartnerProduct) {
            throw new NotFoundHttpException(sprintf('Товар партнера с кодом %d не найден', $command->partnerProductId));
        }

        $partnerProduct->setBaseProductId($baseProduct->getId());
        $em->persist($partnerProduct);
        $em->flush();
    }
}
