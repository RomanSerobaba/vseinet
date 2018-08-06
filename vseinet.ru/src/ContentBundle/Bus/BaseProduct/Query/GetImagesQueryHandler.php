<?php 

namespace ContentBundle\Bus\BaseProduct\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\BaseProductImage;

class GetImagesQueryHandler extends MessageHandler
{
    public function handle(GetImagesQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($query->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %s не найден', $query->id));
        }

        $images = $em->getRepository(BaseProductImage::class)->findBy(['baseProductId' => $product->getId()], ['sortOrder' => 'ASC']);
        $path = $this->getParameter('product.images.web.path');
        foreach ($images as $image) {
            $image->src = $em->getRepository(BaseProductImage::class)->buildSrc($path, $image->getBasename(), $query->size);
        }

        return $images;
    }
}
