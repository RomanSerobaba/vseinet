<?php

namespace AdminBundle\Bus\Supplier\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Competitor;
use AppBundle\Entity\CompetitorProduct;
use AppBundle\Entity\SupplierProduct;

class UnlinkCommandHandler extends MessageHandler
{
    public function handle(UnlinkCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->baseProductId));
        }

        $supplierProduct = $em->getRepository(SupplierProduct::class)->findOneBy(['partnerProductId' => $command->supplierProductId]);
        if (!$supplierProduct instanceof SupplierProduct) {
            throw new NotFoundHttpException(sprintf('Товар поставщика с ид %d не найден', $command->supplierProductId));
        }

        $supplierProduct->setBaseProductId(null);
        $em->persist($supplierProduct);
        $em->flush();

        $partnerProduct = $em->getRepository(SupplierProduct::class)->find($command->supplierProductId);

        $partnerProduct->setBaseProductId(null);
        $em->persist($partnerProduct);
        $em->flush();

        // $q = $em->getConnection()->prepare("
        //     SELECT supplier_product_after_update({$command->baseProductId})
        // ");
        // $q->execute();
    }
}
