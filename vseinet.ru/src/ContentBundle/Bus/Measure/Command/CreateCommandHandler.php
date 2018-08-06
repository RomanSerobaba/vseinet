<?php 

namespace ContentBundle\Bus\Measure\Command;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\Measure;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $measure = new Measure();
        $measure->setName($command->name);

        $em->persist($measure);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $measure->getId());
    }
}