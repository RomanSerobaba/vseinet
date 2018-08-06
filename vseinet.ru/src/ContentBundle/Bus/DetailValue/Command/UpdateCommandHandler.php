<?php 

namespace ContentBundle\Bus\DetailValue\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\DetailValue;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $value = $em->getRepository(DetailValue::class)->find($command->id);
        if (!$value instanceof DetailValue) {
            throw new NotFoundHttpException(sprintf('Значение характеристики %d не найдено', $command->id));
        }

        $value->setValue($command->value);

        $em->persist($value);
        $em->flush();
    }
}