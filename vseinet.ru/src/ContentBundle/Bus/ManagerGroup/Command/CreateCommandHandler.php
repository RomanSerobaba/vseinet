<?php 

namespace ContentBundle\Bus\ManagerGroup\Command;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\ManagerGroup;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $group = new ManagerGroup();
        $group->setName($command->name);

        $em->persist($group);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $group->getId());
    }
}