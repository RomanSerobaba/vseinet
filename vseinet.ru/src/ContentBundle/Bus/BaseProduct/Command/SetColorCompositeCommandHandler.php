<?php

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Bus\ColorComposite\Query\GetFormedValueQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\Color;
use ContentBundle\Entity\ColorComposite;
use ContentBundle\Entity\BaseProductEditLog;
use AppBundle\Enum\BaseProductEditTarget;

class SetColorCompositeCommandHandler extends MessageHandler
{
    public function handle(SetColorCompositeCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository(BaseProduct::class)->find($command->id);
        if (!$product instanceof BaseProduct) {
            throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->id));
        }

        $this->get('query_bus')->handle(new GetFormedValueQuery($command->toArray(), ['categoryId' => $product->getCategoryId()]), $formedValue);

        $composite = $em->getRepository(ColorComposite::class)->findOneBy([
            'schemaType' => $command->schemaType,
            'colorId1' => $command->getColorId(1),
            'colorId2' => $command->getColorId(2),
            'colorId3' => $command->getColorId(3),
            'colorId4' => $command->getColorId(4),
            'withPicture' => $command->withPicture,
            'pictureName' => $command->pictureName,
        ]);
        if (!$composite instanceof ColorComposite) {
            $composite = new ColorComposite();
            $composite->setSchemaType($command->schemaType);
            $composite->setColorId1($command->getColorId(1));
            $composite->setColorId2($command->getColorId(2));
            $composite->setColorId3($command->getColorId(3));
            $composite->setColorId4($command->getColorId(4));
            $composite->setWithPicture($command->withPicture);
            $composite->setPictureName($command->pictureName);
            $composite->setFormedValue($formedValue);

            $em->persist($composite);
        }

        $em->getRepository(BaseProductEditLog::class)->add(
            $product,
            BaseProductEditTarget::COLOR_COMPOSITE,
            null,
            $this->get('user.identity')->getUser(),
            $product->getColorCompositeId(), 
            $composite->getId()
        );

        $product->setColorCompositeId($composite->getId());
        $em->persist($product);

        $em->flush();
    }
}