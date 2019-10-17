<?php

namespace AppBundle\Bus\Main\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Complaint;

class ComplaintCommandHandler extends MessageHandler
{
    public function handle(ComplaintCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $complaint = new Complaint();
        $complaint->setType($command->type);
        $complaint->setManagerName($command->managerName);
        $complaint->setManagerPhone($command->managerPhone);
        $complaint->setText($command->text);
        $command->userData = $this->get('command_bus')->handle(new IdentifyCommand(['userData' => $command->userData]));
        if (null !== $command->userData->userId) {
            $complaint->setUserId($command->userData->userId);
        } else {
            $complaint->setComuserId($command->userData->comuserId);
        }

        $em->persist($complaint);
        $em->flush();
    }
}
