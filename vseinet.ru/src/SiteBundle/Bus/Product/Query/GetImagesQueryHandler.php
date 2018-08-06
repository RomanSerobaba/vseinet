<?php 

namespace SiteBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\BaseProductImage;


class GetImagesQueryHandler extends MessageHandler
{
    public function handle(GetImagesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$baseProduct instanceof BaseProduct){
            throw new NotFoundHttpException();
        }

        $q = $em->createQuery("
            SELECT 
                NEW SiteBundle\Bus\Product\Query\DTO\Image (
                    bpi.id,
                    bpi.basename,
                    bpi.width,
                    bpi.height
                )
            FROM ContentBundle:BaseProductImage AS bpi 
            WHERE bpi.baseProductId = :baseProductId
            ORDER BY bpi.sortOrder 
        ");
        $q->setParameter('baseProductId', $baseProduct->getId());
        $images = $q->getArrayResult();

        return $images;
    }
}
