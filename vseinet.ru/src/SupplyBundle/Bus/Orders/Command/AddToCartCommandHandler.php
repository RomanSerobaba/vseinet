<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\MessageHandler;
use PricingBundle\Entity\ProductPriceLog;
use PricingBundle\Entity\Product;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Oro\ORM\Query\AST\Platform\Functions\Postgresql\Timestamp;

class AddToCartCommandHandler extends MessageHandler
{
    public function handle(AddToCartCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if ($command->price <= 0) {
            throw new BadRequestHttpException('Недопустимое значение цены');
        }

        /**
         * Установка временной цены
         */
        $product = $em->getRepository(Product::class)->findOneBy(['baseProductId' => $command->baseProductId, 'geoCityId' => 1,]);
        if (!$product) {
            throw new BadRequestHttpException('Товар с указанным идентификатором не найден');
        }
        $product->setTemporaryPrice($command->price);
        $em->persist($product);

        /**
         * @TODO Обновление цены у товара (через сфинкс)
         *       Реализовать!
         */

        /**
         * Добавление в лог
         */
        $productPriceLog = new ProductPriceLog();
        $productPriceLog->setPrice($command->price);
        $productPriceLog->setPriceType(ProductPriceLog::PRICE_TYPE_TEMPORARY);
        $productPriceLog->setBaseProductId($command->baseProductId);
        $productPriceLog->setGeoCityId(1);
        $productPriceLog->setOperatedAt(new \DateTime(date('Y-m-d H:i:s')));
        $em->persist($productPriceLog);
    }
}