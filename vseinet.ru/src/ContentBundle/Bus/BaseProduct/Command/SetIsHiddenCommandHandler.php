<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\BaseProduct;

class SetIsHiddenCommandHandler extends MessageHandler
{
    public function handle(SetIsHiddenCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository(BaseProduct::class)->findBy(['id' => $command->ids]);
        if (empty($products)) {
            throw new BadRequestHttpException('Выберите товары для показа / скрытия');
        }

        foreach ($products as $product) {
            $product->setIsHidden($command->isHidden);
            $em->persist($product);
        }

        $em->flush();
    }
}