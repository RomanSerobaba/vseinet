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

        $q = $em->createNativeQuery('
            INSERT INTO base_product_history (
                base_product_id,
                user_id,
                object,
                new_value
            )
            SELECT
                :base_product_id,
                :user_id,
                :object,
                :new_value
        ');
        $q->execute([
            'base_product_id' => $baseProduct->getId(),
            'user_id' => $this->getUser()->getId(),
            'object' => 'partner_product',
            'new_value' => $partnerProduct->getId(),
        ]);

        $em->flush();
    }
}
