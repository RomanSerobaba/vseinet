<?php 

namespace ContentBundle\Bus\ManagerGroup\Command; 

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\ManagerGroup;

class UpdateCommandHandler extends MessageHandler
{
    public function handle(UpdateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository(ManagerGroup::class)->find($command->id);
        if (!$group instanceof ManagerGroup) {
            throw new NotFoundHttpException(sprintf('Группа контент-менеджеров %d не найдена', $command->id));
        } 

        $group->setName($command->name);
        
        $em->persist($group);
        $em->flush();
    }
}