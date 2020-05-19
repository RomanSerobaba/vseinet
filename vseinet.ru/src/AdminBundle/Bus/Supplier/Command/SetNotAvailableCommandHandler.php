<?php

namespace AdminBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\SupplierProduct;
use AppBundle\Enum\ProductAvailabilityCode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetNotAvailableCommandHandler extends MessageHandler
{
    public function handle(SetNotAvailableCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $supplierProduct = $em->getRepository(SupplierProduct::class)->findOneByPartnerProductId($command->partnerProductId);
        if (!$supplierProduct instanceof SupplierProduct) {
            throw new NotFoundHttpException(sprintf('Товар партнера с кодом %d не найден', $command->partnerProductId));
        }

        $supplierProduct->setProductAvailabilityCode(ProductAvailabilityCode::OUT_OF_STOCK);
        $em->persist($supplierProduct);
        $em->flush();
    }
}
