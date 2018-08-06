<?php 

namespace SiteBundle\Bus\Catalog\Query;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\BaseProductImage;

class GetProductsQueryHandler extends MessageHandler
{
    public function handle(GetProductsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();
        $cityId = $this->get('city.identity')->getId();
        $criteria = 0 === $cityId ? "p.geoCityId IS NULL" : "p.geoCityId = {$cityId}";

        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Catalog\Query\DTO\Product (
                    bp.id,
                    bp.name,
                    bpi.basename,
                    p.productAvailabilityCode,
                    p.price,
                    p.priceType,
                    bpd.shortDescription,
                    bp.minQuantity,
                    bp.updatedAt
                )
            FROM ContentBundle:BaseProduct bp 
            INNER JOIN ContentBundle:BaseProductData bpd WITH bpd.baseProductId = bp.id
            INNER JOIN PricingBundle:Product p WITH p.baseProductId = bp.id AND {$criteria}
            LEFT OUTER JOIN ContentBundle:BaseProductImage bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
            WHERE bp.id IN (:ids)
        ");
        $q->setParameter('ids', $query->ids);
        $products = $q->getResult('IndexByHydrator');

        $image = $em->getRepository(BaseProductImage::class);
        $path = $this->getParameter('product.images.web.path');
        array_walk($products, function(&$product) use ($image, $path, $query) {
            $product->previewSrc = $image->buildSrc($path, $product->previewSrc, $query->previewSize);
        });

        $sorted = [];
        foreach ($query->ids as $id) {
            if (isset($products[$id])) {
                $sorted[] = $products[$id];
            }
        }

        return $sorted;
    }
}
