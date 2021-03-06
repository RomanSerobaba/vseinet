<?php

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPriceLog;
use AppBundle\Enum\ProductPriceTypeCode;
use AppBundle\Enum\UserRole;
use Doctrine\ORM\AbstractQuery;

class SetPriceCommandHandler extends MessageHandler
{
    public function handle(SetPriceCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->id));
        }

        // $product = $em->getRepository(Product::class)->findOneBy([
        //     'baseProductId' => $baseProduct->getId(),
        //     'geoCityId' => 0,//$this->getGeoCity()->getId(),
        // ]);
        // if (!$product instanceof Product) {
        //     $product = $em->getRepository(Product::class)->findOneBy([
        //         'baseProductId' => $baseProduct->getId(),
        //         'geoCityId' => 0,
        //     ]);
        //     $q = $em->createQuery('
        //         SELECT r.geoPointId
        //         FROM AppBundle:Representative AS r
        //         JOIN AppBundle:GeoPoint AS gp WITH gp.id = r.geoPointId
        //         WHERE gp.geoCityId = :geoCityId AND r.isActive = TRUE AND r.isCentral = TRUE
        //     ')->setParameter('geoCityId', $this->getGeoCity()->getRealId());
        //     $geoPointId = $q->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR);

        //     if ($geoPointId) {
        //         $product = clone $product;
        //         $product->setGeoCityId($this->getGeoCity()->getId());
        //         $em->persist($product);
        //         $em->flush();
        //     }
        // }

        $product = $em->getRepository(Product::class)->findOneBy(['baseProductId' => $command->id, 'geoCityId' => 0,]);

        if ($product->getPrice() > $command->price && !$this->getUser()->isRoleIn([UserRole::ADMIN, UserRole::PURCHASER]) && ($baseProduct->getSupplierPrice() > $command->price || !in_array($this->getUser()->getId(), [4980, 1501, 65621, 12538, 106265]))) {
            throw new BadRequeetsHttpException(sprintf('У вас нет прав на снижение цены, обратитесь к уполномоченному'));
        }

        switch ($command->type) {
            case ProductPriceTypeCode::MANUAL:
                $product->setManualPrice($command->price);
                $product->setManualPriceOperatedAt(new \DateTime());
                $product->setManualPriceOperatedBy($this->getUser()->getId());
                break;

            case ProductPriceTypeCode::ULTIMATE:
                $product->setUltimatePrice($command->price);
                $product->setUltimatePriceOperatedAt(new \DateTime());
                $product->setUltimatePriceOperatedBy($this->getUser()->getId());
                break;

            case ProductPriceTypeCode::TEMPORARY:
                $product->setTemporaryPrice($command->price);
                $product->setTemporaryPriceOperatedAt(new \DateTime());
                $product->setTemporaryPriceOperatedBy($this->getUser()->getId());
                break;

            default:
                throw new BadRequeetsHttpException(sprintf('Тип цены %s нельзя установить вручную', $command->type));
        }

        $em->persist($product);

        $log = new ProductPriceLog();
        $log->setBaseproductId($baseProduct->getId());
        $log->setGeoCityId(0/*$this->getGeoCity()->getId()*/);
        $log->setPrice($command->price);
        $log->setPriceTypeCode($command->type);
        $log->setOperatedBy($this->getUser()->getId());
        $log->setOperatedAt(new \DateTime());
        $em->persist($log);
        $em->flush();
    }
}
