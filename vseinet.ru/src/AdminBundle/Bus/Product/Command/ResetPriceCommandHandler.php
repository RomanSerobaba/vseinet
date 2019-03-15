<?php

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPriceLog;
use AppBundle\Enum\ProductPriceType;

class ResetPriceCommandHandler extends MessageHandler
{
    public function handle(ResetPriceCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->id));
        }

        $product = $em->getRepository(Product::class)->findOneBy([
            'baseProductId' => $baseProduct->getId(),
            'geoCityId' => $this->getGeoCity()->getId(),
        ]);
        if ($product instanceof Product) {
            if ($price = $product->getTemporaryPrice()) {
                $type = ProductPriceType::TEMPORARY;
                $product->setTemporaryPrice(null);
            } elseif ($price = $product->getUltimatePrice()) {
                $type = ProductPriceType::ULTIMATE;
                $product->setUltimatePrice(null);
            } elseif ($price = $product->getManualPrice()) {
                $type = ProductPriceType::MANUAL;
                $product->setManualPrice(null);
            } else {
                throw new BadRequeetsHttpException('У товара не задана ручная цена');
            }
            $em->persist($product);

            $log = new ProductPriceLog();
            $log->setBaseProductId($baseProduct->getId());
            $log->setGeoCityId($this->getGeoCity()->getId());
            $log->setPrice(null);
            $log->setPriceType($type);
            $log->setOperatedBy($this->getUser()->getId());
            $log->setOperatedAt(new \DateTime());
            $em->persist($log);

            $em->flush();
        }
    }
}
