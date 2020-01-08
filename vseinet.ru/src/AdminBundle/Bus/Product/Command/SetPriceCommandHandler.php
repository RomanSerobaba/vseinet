<?php

namespace AdminBundle\Bus\Product\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPriceLog;
use AppBundle\Enum\ProductPriceTypeCode;
use AppBundle\Enum\RepresentativeTypeCode;
use AppBundle\Enum\UserRole;
use Doctrine\ORM\AbstractQuery;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SetPriceCommandHandler extends MessageHandler
{
    public function handle(SetPriceCommand $command)
    {
        $em = $this->getDoctrine()->getManager();
        $representative = $this->get('representative.identity')->getRepresentative();
        if (!$representative || RepresentativeTypeCode::FRANCHISER !== $representative->getType()) {
            $geoCityId = 0;
        }

        if (!$command->price) {
            $this->get('command_bus')->handle(new ResetPriceCommand(['id' => $command->id]));

            return;
        }

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->id));
        }

        $product = $em->getRepository(Product::class)->findOneBy(['baseProductId' => $command->id, 'geoCityId' => $geoCityId,]);

        if ($product->getPrice() > $command->price && !$this->getUser()->isRoleIn([UserRole::ADMIN, UserRole::PURCHASER]) && ($baseProduct->getSupplierPrice() > $command->price || !in_array($this->getUser()->getId(), [4980, 1501, 65621, 12538, 106265]))) {
            throw new BadRequestHttpException(sprintf('У вас нет прав на снижение цены, обратитесь к уполномоченному'));
        }

        if (in_array($command->type, [ProductPriceTypeCode::ULTIMATE, ProductPriceTypeCode::MANUAL]) && !$this->getUser()->isRoleIn([UserRole::ADMIN, UserRole::PURCHASER]) && !in_array($this->getUser()->getId(), [4980, 1501])) {
            throw new BadRequestHttpException(sprintf('У вас нет прав на установку фиксированной цены, обратитесь к уполномоченному'));
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
                throw new BadRequestHttpException(sprintf('Тип цены %s нельзя установить вручную', $command->type));
        }

        $em->persist($product);

        $log = new ProductPriceLog();
        $log->setBaseproductId($baseProduct->getId());
        $log->setGeoCityId($geoCityId);
        $log->setPrice($command->price);
        $log->setPriceTypeCode($command->type);
        $log->setOperatedBy($this->getUser()->getId());
        $log->setOperatedAt(new \DateTime());
        $em->persist($log);
        $em->flush();
    }
}
