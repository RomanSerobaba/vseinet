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
        $complaint->setCreatedAt(new \DateTime());
        if (null !== $command->userData->userId) {
            $complaint->setUserId($command->userData->userId);
            // @todo: save contactIds
        } else {
            $complaint->setComuserId($command->userData->comuserId);
        }
        $complaint->setIsChecked(false);
        
        $em->persist($complaint);
        $em->flush();
    }
}
