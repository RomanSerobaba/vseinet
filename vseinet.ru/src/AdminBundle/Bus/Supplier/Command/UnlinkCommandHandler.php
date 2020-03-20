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

        $supplierProduct = $em->getRepository(SupplierProduct::class)->find($command->supplierProductId);
        if (!$supplierProduct instanceof SupplierProduct) {
            throw new NotFoundHttpException(sprintf('Товар поставщика с кодом %d не найден', $command->supplierProductId));
        }

        $competitor = $em->getRepository(Competitor::class)->findOneBy(['supplierId' => $supplierProduct->getSupplierId()]);

        if ($competitor) {
            $competitorProduct = $em->getRepository(CompetitorProduct::class)->findOneBy([
                'code' => $supplierProduct->getCode(),
                'competitorId' => $competitor->getId(),
            ]);

            if ($competitorProduct) {
                $em->remove($competitorProduct);
                $em->flush();
            }

            $competitorProduct = $em->getRepository(CompetitorProduct::class)->findOneBy([
                'baseProductId' => $supplierProduct->getBaseProductId(),
                'competitorId' => $competitor->getId(),
            ]);

            if ($competitorProduct) {
                $em->remove($competitorProduct);
                $em->flush();
            }
        }

        $supplierProduct->setBaseProductId(null);
        $em->persist($supplierProduct);
        $em->flush();

        // $q = $em->getConnection()->prepare("
        //     SELECT supplier_product_after_update({$command->baseProductId})
        // ");
        // $q->execute();
    }
}
