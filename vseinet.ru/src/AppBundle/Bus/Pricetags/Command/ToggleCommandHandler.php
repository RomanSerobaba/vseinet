<?php

namespace AppBundle\Bus\Pricetags\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\ProductPricetagBuffer;
use AppBundle\Entity\BaseProduct;

class ToggleCommandHandler extends MessageHandler
{
    public function handle(ToggleCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $pricetagBuffer = $em->getRepository(ProductPricetagBuffer::class)->findOneBy([
            'baseProductId' => $command->baseProductId,
            'createdBy' => $this->getUser()->getId(),
        ]);

        if ($pricetagBuffer instanceof ProductPricetagBuffer) {
            $em->remove($pricetagBuffer);
            $em->flush();

            return false;
        }

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар с кодом %d не найден', $command->baseProductId));
        }

        $pricetagBuffer = new ProductPricetagBuffer();
        $pricetagBuffer->setBaseProductId($baseProduct->getId());
        $pricetagBuffer->setCreatedBy($this->getUser()->getId());
        $pricetagBuffer->setQuantity(1);

        $em->persist($pricetagBuffer);
        $em->flush($pricetagBuffer);

        return true;
    }
}
