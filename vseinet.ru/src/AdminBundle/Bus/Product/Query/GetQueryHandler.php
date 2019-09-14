<?php

namespace AdminBundle\Bus\Product\Query;

use AppBundle\bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\Product;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($query->id);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $query->id));
        }

        $product = $em->getRepository(Product::class)->findOneBy([
            'baseProductId' => $baseProduct->getCanonicalId(),
            'geoCityId' => $this->getGeoCity()->getId(),
        ]);
        if (!$product instanceof Product) {
            $product = $em->getRepository(Product::class)->findOneBy([
                'baseProductId' => $baseProduct->getCanonicalId(),
                'geoCityId' => 0,
            ]);
        }

        return new DTO\Product($product->getBaseProductId(), $product->getPrice(), $product->getPriceType());
    }
}
