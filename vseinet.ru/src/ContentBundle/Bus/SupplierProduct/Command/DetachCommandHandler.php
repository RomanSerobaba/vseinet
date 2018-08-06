<?php 

namespace ContentBundle\Bus\SupplierProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\SupplierProduct;
use ContentBundle\Entity\BaseProduct;

class DetachCommandHandler extends MessageHandler
{
    public function handle(DetachCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $supplierProduct = $em->getRepository(SupplierProduct::class)->find($command->id);
        if (!$supplierProduct instanceof SupplierProduct) {
            throw new NotFoundHttpException(sprintf('Товар поставщика %d не найден', $command->id));
        }

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %s не найден', $command->baseProductId));
        }

        $supplierProduct->setBaseProductId(null);

        $em->persist($supplierProduct);
        $em->flush();
    }
}
