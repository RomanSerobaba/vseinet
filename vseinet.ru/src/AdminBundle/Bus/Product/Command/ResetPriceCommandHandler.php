<?php

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPriceLog;
use AppBundle\Enum\ProductPriceType;
use Doctrine\ORM\AbstractQuery;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResetPriceCommandHandler extends MessageHandler
{
    public function handle(ResetPriceCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->id));
        }

        $q = $em->createQuery('
            SELECT r.geoPointId
            FROM AppBundle:Representative AS r
            JOIN AppBundle:GeoPoint AS gp WITH gp.id = r.geoPointId
            WHERE gp.geoCityId = :geoCityId AND r.isActive = TRUE AND r.isCentral = TRUE
        ')->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        $geoPointId = $q->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        $product = $em->getRepository(Product::class)->findOneBy([
            'baseProductId' => $baseProduct->getId(),
            'geoCityId' => $geoPointId ? $this->getGeoCity()->getId() : 0,
        ]);
        if ($product instanceof Product) {
            if ($product->getTemporaryPrice()) {
                $type = ProductPriceType::TEMPORARY;
                $product->setTemporaryPrice(null);
            } elseif ($product->getUltimatePrice()) {
                $type = ProductPriceType::ULTIMATE;
                $product->setUltimatePrice(null);
            } elseif ($product->getManualPrice()) {
                $type = ProductPriceType::MANUAL;
                $product->setManualPrice(null);
            } else {
                throw new BadRequestHttpException('У товара не задана ручная цена');
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
