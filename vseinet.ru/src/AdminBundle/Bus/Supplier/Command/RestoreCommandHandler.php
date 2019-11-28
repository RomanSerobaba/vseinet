<?php

namespace AdminBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\SupplierProduct;

class RestoreCommandHandler extends MessageHandler
{
    public function handle(RestoreCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->baseProductId));
        }

        $supplierProduct = $em->getRepository(SupplierProduct::class)->find($command->supplierProductId);
        if (!$supplierProduct instanceof SupplierProduct) {
            throw new NotFoundHttpException(sprintf('Товар поставщика с кодом %d не найден', $command->supplierProductId));
        }

        $supplierProduct->setBaseProductId($baseProduct->getId());
        $em->persist($supplierProduct);
        $em->flush();

        $q = $em->getConnection()->prepare("
            SELECT supplier_pricelist_after_load({$command->baseProductId})
        ");
        $q->execute();
    }
}
