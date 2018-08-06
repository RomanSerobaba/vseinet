<?php 

namespace ContentBundle\Bus\BaseProduct\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ContentBundle\Bus\BaseProduct\Query\DTO\BaseProduct;
use ContentBundle\Entity\Category;
use AppBundle\Enum\CategoryTpl;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\BaseProduct\Query\DTO\BaseProduct (
                    bp.id,
                    bp.name,
                    bp.categoryId,
                    bp.sectionId,
                    bp.brandId,
                    bp.colorCompositeId,
                    bpdt.model,
                    bpdt.exname,
                    bpdt.manufacturerLink,
                    bpdt.manualLink,
                    COALESCE(bpd.description, '')
                )
            FROM ContentBundle:BaseProduct bp 
            INNER JOIN ContentBundle:BaseProductData bpdt WITH bpdt.baseProductId = bp.id 
            LEFT OUTER JOIN ContentBundle:BaseProductDescription bpd WITH bpd.baseProductId = bp.id 
            WHERE bp.id = :id
        ");
        $q->setParameter('id', $query->id);
        $product = $q->getOneOrNullResult();
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $query->id));   
        }

        return $product;
    }
}
