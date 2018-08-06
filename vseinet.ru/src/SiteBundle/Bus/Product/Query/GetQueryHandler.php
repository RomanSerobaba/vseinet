<?php 

namespace SiteBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\NoResultException;
use AppBundle\Enum\BaseProductImage;
use SiteBundle\Bus\Cart\Query\GetMiniQuery as GetMiniCartQuery;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT bpml.newId
            FROM ContentBundle:BaseProductMergeLog bpml 
            WHERE bpml.oldId = :id 
        ");
        $q->setParameter('id', $query->id);
        try {
            $id = $q->getSingleScalarResult();   
            
        } catch (NoResultException $e) {
            $id = $query->id;
        }

        $cityId = $this->get('city.identity')->getId();
        $criteria = 0 === $cityId ? "p.geoCityId IS NULL" : "p.geoCityId = {$cityId}";

        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Product\Query\DTO\Product (
                    bp.id,
                    bp.name,
                    bpd.exname,
                    bp.categoryId,
                    bp.brandId,
                    p.productAvailabilityCode,
                    p.price,
                    p.priceType,
                    bp.minQuantity,
                    bpd.model,
                    bpd.manufacturerLink,
                    bpd.manualLink,
                    d.description,
                    bp.estimate
                )
            FROM ContentBundle:BaseProduct AS bp 
            INNER JOIN ContentBundle:BaseProductData AS bpd WITH bpd.baseProductId = bp.id
            INNER JOIN PricingBundle:Product AS p WITH p.baseProductId = bp.id AND {$criteria}
            LEFT OUTER JOIN ContentBundle:BaseProductDescription AS d WITH d.baseProductId = bp.id 
            WHERE bp.id = :id
        ");
        $q->setParameter('id', $id);
        $product = $q->getSingleResult();
        if (!$product instanceof DTO\Product) {
            throw new NotFoundHttpException();
        }
        $product->isCanonical = $id == $query->id;

        return $product;
    }
}
