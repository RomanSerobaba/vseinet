<?php

namespace AdminBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\SupplierProduct;
use AppBundle\Enum\ProductAvailabilityCode;

class SetNotAvailableCommandHandler extends MessageHandler
{
    public function handle(SetNotAvailableCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $supplierProduct = $em->getRepository(SupplierProduct::class)->find($command->supplierProductId);
        if (!$supplierProduct instanceof SupplierProduct) {
            throw new NotFoundHttpException(sprintf('Товар поставщика с кодом %d не найден', $command->supplierProductId));
        }

        $supplierProduct->setProductAvailabilityCode(ProductAvailabilityCode::OUT_OF_STOCK);
        $em->persist($supplierProduct);
        $em->flush();

        $q = $em->getConnection()->prepare("
            SELECT supplier_product_after_update({$supplierProduct->getBaseProductId()})
        ");
        $q->execute();
    }
}
