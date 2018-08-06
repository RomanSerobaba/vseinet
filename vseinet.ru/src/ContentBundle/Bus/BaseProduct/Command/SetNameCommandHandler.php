<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\BaseProductEditLog;
use AppBundle\Enum\BaseProductEditTarget;

class SetNameCommandHandler extends MessageHandler
{
    public function handle(SetNameCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find(['baseProductId' => $command->id]);
        if (!$product instanceof BaseProduct) {
            throw new BadRequestHttpException(sprintf('Товар %d не найден', $command->id));
        }

        if ($product->getName() != $command->name) {
            $em->getRepository(BaseProductEditLog::class)->add(
                $product->getId(), 
                BaseProductEditTarget::NAME,
                null,
                $this->get('user.identity')->getUser(), 
                $product->getName(), 
                $command->name
            );
            $product->setName($command->name);
        }
        $em->merge($product);
        $em->flush();
    }
}
