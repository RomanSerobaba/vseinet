<?php 

namespace ContentBundle\Bus\SupplierProductTransfer\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\BaseProductImage;

class GetBaseProductsQueryHandler extends MessageHandler
{
    public function handle(GetBaseProductsQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository(Category::class)->find($query->categoryId);
        if (!$category instanceof Category) {
            throw new NotFoundHttpException(sprintf('Категория %s не найдена', $query->categoryId));
        }

        $spec = new Specification\Catalog();
        $where = $spec->build($query->filter, $this->get('user.identity')->getUser()->getCityId());

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT 
                NEW ContentBundle\Bus\SupplierProductTransfer\Query\DTO\BaseProduct (
                    bp.id,
                    bp.name,
                    bp.categoryId,
                    p.price,
                    bpi.basename,
                    CASE 
                        WHEN {$spec->isNew()} THEN 'new'
                        WHEN {$spec->isActive()} THEN 'active'
                        WHEN {$spec->isOld()} THEN 'old'
                        WHEN {$spec->isHidden()} THEN 'hidden'
                        ELSE ''
                    END
                )
            FROM ContentBundle:BaseProduct bp
            INNER JOIN PricingBundle:Product p WITH p.baseProductId = bp.id 
            LEFT OUTER JOIN ContentBundle:BaseProductImage bpi WITH bpi.baseProductId = bp.id AND bpi.sortOrder = 1
            WHERE bp.categoryId = :categoryId {$where}
            ORDER BY bp.name 
        ");
        $q->setParameter('categoryId', $category->getAliasForId() ?: $category->getId());
        $products = $q->getArrayResult();

        $path = $this->getParameter('product.images.web.path');
        foreach ($products as $product) {
            $product->imageSrc = $em->getRepository(BaseProductImage::class)->buildSrc($path, $product->imageSrc, 'md');
        }

        return $products;
    } 
}
