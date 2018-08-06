<?php 

namespace ContentBundle\Bus\DetailGroup\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\DetailGroup;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(DetailGroup::class)->find($command->id);
        if (!$group instanceof DetailGroup) {
            throw new NotFoundHttpException(sprintf('Группа характеристик %d  не найдена', $command->id));
        }

        $group->setName($command->name);

        $em->persist($group);
        $em->flush();
    }
}