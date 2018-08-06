<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\BaseProductEditLog;
use AppBundle\Enum\BaseProductEditTarget;

class DeleteColorCompositeCommandHandler extends MessageHandler
{
    public function handle(DeleteColorCompositeCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->id));
        }

        $em->getRepository(BaseProductEditLog::class)->add(
            $product, 
            BaseProductEditTarget::COLOR_COMPOSITE, 
            null,
            $this->get('user.identity')->getUser(), 
            $product->getColorCompositeId(), 
            null
        );

        $product->setColorCompositeId(null);

        $em->persist($product);
        $em->flush();
    }
}