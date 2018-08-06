<?php 

namespace ContentBundle\Bus\ParserProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ParserProduct;
use ContentBundle\Entity\BaseProduct;

class UnlinkCommandHandler extends MessageHandler
{
    public function handle(UnlinkCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $parserProduct = $em->getRepository(ParserProduct::class)->find($command->id);
        if (!$parserProduct instanceof ParserProduct) {
            throw new NotFoundHttpException(sprintf('Товар парсера %d не найден', $command->id));
        }

        $baseProduct = $em->getRepository(BaseProduct::class)->find($command->baseProductId);
        if (!$baseProduct instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %s не найден', $command->baseProductId));
        }

        $parserProduct->setBaseProductId(null);

        $em->persist($parserProduct);
        $em->flush();
    }
}
