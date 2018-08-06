<?php 

namespace ContentBundle\Bus\DetailValue\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\DetailValue;

class SetIsVerifiedCommandHandler extends MessageHandler
{
    public function handle(SetIsVerifiedCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $value = $em->getRepository(DetailValue::class)->find($command->id);
        if (!$value instanceof DetailValue) {
            throw new NotFoundHttpException(sprintf('Значение характеристики %d не найдено', $command->id));
        }

        $value->setIsVerified($command->isVerified);

        $em->persist($value);
        $em->flush();
       
    }
}