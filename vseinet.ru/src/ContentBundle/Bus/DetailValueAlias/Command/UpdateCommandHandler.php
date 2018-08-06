<?php 

namespace ContentBundle\Bus\DetailValueAlias\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\DetailValue;
use ContentBundle\Entity\DetailValueAlias;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $alias = $em->getRepository(DetailValueAlias::class)->find($command->id);
        if (!$alias instanceof DetailValueAlias) {
            throw new NotFoundHttpException(sprintf('Псевдоним значения характеристики %d не найден', $command->id));
        }

        $value = $em->getRepository(DetailValue::class)->find($alias->getValueId());
        if ($value->getValue() == $command->value) {
            $em->remove($alias);
        }
        else {
            $alias->setValue($command->value);
            $em->persist($alias);
        }

        $em->flush();
    }
}