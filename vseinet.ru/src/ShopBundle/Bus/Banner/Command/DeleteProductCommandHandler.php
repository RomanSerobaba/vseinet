<?php 

namespace ShopBundle\Bus\Banner\Command;

use AppBundle\Bus\Message\MessageHandler;
use ShopBundle\Entity\BannerMainProductData;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DeleteProductCommandHandler extends MessageHandler
{
    public function handle(DeleteProductCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = $em->getRepository(BannerMainProductData::class)->find($command->id);

        if (!$model instanceof BannerMainProductData) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->id));
        }
                
        $em->remove($model);
        $em->flush();
    }
}