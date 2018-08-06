<?php 

namespace ContentBundle\Bus\SupplierProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\SupplierProduct;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\BaseProductData;
use ContentBundle\Entity\BaseProductDescription;
use SupplyBundle\Entity\SupplierProductTransferLog;
use ContentBundle\Entity\ParserProduct;
use PricingBundle\Entity\Product;
use AppBundle\Enum\ProductPriceType;

class TransferCommandHandler extends MessageHandler
{
    public function handle(TransferCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $supplierProducts = $em->getRepository(SupplierProduct::class)->findBy(['id' => $command->ids]);
        if (empty($supplierProducts)) {
            throw new BadRequestHttpException('Выберите хотя бы один товар поставщика');
        }

        $category = $em->getRepository(Category::class)->find($command->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %s не найдена', $command->categoryId));
        }

        foreach ($supplierProducts as $supplierProduct) {
            $baseProduct = new BaseProduct();
            $baseProduct->setName($supplierProduct->getName());
            $baseProduct->setCategoryId($category->getId());
            $baseProduct->setBrandId($supplierProduct->getBrandId());
            $baseProduct->setCreatedAt(new \DateTime());
            $baseProduct->setMinQuantity($supplierProduct->getMinQuantity());
            $baseProduct->setIsHidden(false);
            $baseProduct->setEstimate(0);
            $baseProduct->setSupplierId($supplierProduct->getSupplierId());
            $baseProduct->setSupplierPrice($supplierProduct->getPrice());
            $baseProduct->setSupplierAvailabilityCode($supplierProduct->getAvailabilityCode());
            $baseProduct->setFreeReserve(0);
            $em->persist($baseProduct);
            $em->flush($baseProduct);

            $baseProductData = new BaseProductData();
            $baseProductData->setBaseProductId($baseProduct->getId());
            $em->persist($baseProductData);

            if ($supplierProduct->getDescription()) {
                $baseProductDescription = new BaseProductDescription();
                $baseProductDescription->setBaseProductId($baseProduct->getId());
                $baseProductDescription->setDescription($supplierProduct->getDescription());
                $em->persist($baseProductDescription);
            }

            $supplierProduct->setBaseProductId($baseProduct->getId());
            $supplierProduct->setIsHidden(false);
            $em->persist($supplierProduct);

            $log = new SupplierProductTransferLog();
            $log->setSupplierProductId($supplierProduct->getId());
            $log->setBaseProductId($baseProduct->getId());
            if ($this->get('user.identity')->isEmployee()) {
                $log->setTransferedBy($this->get('user.identity')->getUser()->getId());
            }
            $log->setTransferedAt(new \DateTime());
            $em->persist($log);

            $parserProduct = $em->getRepository(ParserProduct::class)->findOneBy([
                'supplierProductId' => $supplierProduct->getId(),
                'baseProductId' => null,
            ]);
            if ($parserProduct instanceof ParserProduct) {
                $parserProduct->setBaseProductId($baseProduct->getId());
                $em->persist($parserProduct);
            }

            $product = new Product();
            $product->setBaseProductId($baseProduct->getId());
            $product->setProductAvailabilityCode($baseProduct->getSupplierAvailabilityCode());
            $product->setPrice(0);
            $product->setPriceType(ProductPriceType::PRICELIST);
            $product->setPriceTime($baseProduct->getCreatedAt());
            $product->setCreatedAt($baseProduct->getCreatedAt());
            $product->setRating(0);
            $em->persist($product);
        }

        $em->flush();
    }
}
