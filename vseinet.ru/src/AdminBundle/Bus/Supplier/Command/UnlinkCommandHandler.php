<?php

namespace AdminBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\PartnerProduct;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UnlinkCommandHandler extends MessageHandler
{
    public function handle(UnlinkCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->baseProductId));
        }

        $partnerProduct = $em->getRepository(PartnerProduct::class)->find($command->partnerProductId);
        if (!$partnerProduct instanceof PartnerProduct) {
            throw new NotFoundHttpException(sprintf('Товар партнера с ид %d не найден', $command->partnerProductId));
        }

        $partnerProduct->setBaseProductId(null);
        $em->persist($partnerProduct);

        $q = $em->createNativeQuery('
            INSERT INTO base_product_history (
                base_product_id,
                user_id,
                object,
                old_value
            )
            SELECT
                :base_product_id,
                :user_id,
                :object,
                :old_value
        ', new ResultSetMapping());
        $q->execute([
            'base_product_id' => $baseProduct->getId(),
            'user_id' => $this->getUser()->getId(),
            'object' => 'partner_product',
            'old_value' => $partnerProduct->getId(),
        ]);

        $em->flush();
    }
}
