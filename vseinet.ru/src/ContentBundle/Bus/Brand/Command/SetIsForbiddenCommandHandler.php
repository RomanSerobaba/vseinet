<?php

namespace ContentBundle\Bus\Brand\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\Brand;

class SetIsForbiddenCommandHandler extends MessageHandler 
{
    public function handle(SetIsForbiddenCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $brand = $em->getRepository(Brand::class)->find($command->id);
        if (!$brand instanceof Brand) {
            throw new NotFoundHttpException(sprintf('Бренд %s не найден', $command->id));
        }

        $brand->setIsForbidden($command->value);

        $em->persist($brand);
        $em->flush();
    }
}