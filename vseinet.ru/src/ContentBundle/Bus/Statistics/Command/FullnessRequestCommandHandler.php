<?php 

namespace ContentBundle\Bus\Statistics\Command;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\Fullness;

class FullnessRequestCommandHandler extends MessageHandler
{
    public function handle(FullnessRequestCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $fullness = $em->getRepository(Fullness::class)->findOneBy(['subject' => $command->subject]);
        if (!$fullness instanceof Fullness) {
            $fullness = new Fullness();
            $fullness->setSubject($command->subject);
        }
        $fullness->setUpdatedAt(null);

        $em->persist($fullness);
        $em->flush();

        $this->get('old_sound_rabbit_mq.execute.command_producer')->publish(json_encode([
            'command' => 'content:fillness:refresh',
            'args' => [
                'subject' => $command->subject,
            ],
        ]));
    }
}