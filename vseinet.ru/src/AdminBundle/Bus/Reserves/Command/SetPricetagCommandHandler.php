<?php

namespace AdminBundle\Bus\Reserves\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\ProductPricetagSettings;

class SetPricetagCommandHandler extends MessageHandler
{
    public function handle(SetPricetagCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->id));
        }

        $settings = $em->getRepository(ProductPricetagSettings::class)->findOneBy([
            'baseProductId' => $baseProduct->getId(),
            'geoPointId' => $command->geoPointId,
        ]);

        if (!$settings instanceof ProductPricetagSettings) {
            $settings = new ProductPricetagSettings();
            $settings->setGeoPointId($command->geoPointId);
            $settings->setBaseProductId($command->id);
        }

        $settings->setHandmadeCreatedAt(new \DateTime());
        $settings->setHandmadeCreatedBy($this->getUser()->getId());
        $settings->setHandmadePrice($command->price ?? null);

        $em->persist($settings);
        $em->flush();
    }
}
