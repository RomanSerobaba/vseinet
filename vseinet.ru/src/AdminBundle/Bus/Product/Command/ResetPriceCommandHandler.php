<?php

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPriceLog;
use AppBundle\Entity\Representative;
use AppBundle\Enum\ProductPriceTypeCode;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\Enum\UserRole;
use Doctrine\ORM\AbstractQuery;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ResetPriceCommandHandler extends MessageHandler
{
    public function handle(ResetPriceCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $representative = $this->get('representative.identity')->getRepresentative();
        if (!$representative || RepresentativeTypeCode::FRANCHISER !== $representative->getType()) {
            $geoCityId = 0;
        }

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->id));
        }

        $product = $em->getRepository(Product::class)->findOneBy(['baseProductId' => $command->id, 'geoCityId' => $geoCityId,]);

        if ($product->getTemporaryPrice()) {
            $type = ProductPriceTypeCode::TEMPORARY;
            $product->setTemporaryPrice(null);
        } elseif ($product->getUltimatePrice()) {
            if (!$this->getUser()->isRoleIn([UserRole::ADMIN, UserRole::PURCHASER]) && !in_array($this->getUser()->getId(), [4980, 1501])) {
                throw new BadRequestHttpException(sprintf('У вас нет прав на сброс фиксированной цены, обратитесь к уполномоченному'));
            }
            $type = ProductPriceTypeCode::ULTIMATE;
            $product->setUltimatePrice(null);
        } elseif ($product->getManualPrice()) {
            if (!$this->getUser()->isRoleIn([UserRole::ADMIN, UserRole::PURCHASER]) && !in_array($this->getUser()->getId(), [4980, 1501])) {
                throw new BadRequestHttpException(sprintf('У вас нет прав на сброс фиксированной цены, обратитесь к уполномоченному'));
            }
            $type = ProductPriceTypeCode::MANUAL;
            $product->setManualPrice(null);
        } else {
            $product->getTemporaryPrice($product->getPrice());
            $em->flush($product);
            $product->getTemporaryPrice(null);
            return;
        }

        $log = new ProductPriceLog();
        $log->setBaseProductId($baseProduct->getId());
        $log->setGeoCityId($geoCityId);
        $log->setPrice(null);
        $log->setPriceTypeCode($type);
        $log->setOperatedBy($this->getUser()->getId());
        $log->setOperatedAt(new \DateTime());
        $em->persist($log);
        $em->flush($log);
    }
}
