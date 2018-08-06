<?php 

namespace ContentBundle\Bus\BaseProductImage\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProductImage;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $image = $em->getRepository(BaseProductImage::class)->find($query->id);
        if (!$image instanceof BaseProductImage) {
            throw new NotFoundHttpException(sprintf('Изображение %s не найдено', $query->id));
        }
        $path = $this->getParameter('product.images.web.path');
        $image->src = $em->getRepository(BaseProductImage::class)->buildSrc($path, $image->getBasename(), $query->size);

        return $image;
    }
}
