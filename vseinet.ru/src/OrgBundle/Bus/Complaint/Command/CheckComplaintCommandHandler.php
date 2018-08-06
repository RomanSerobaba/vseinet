<?php 

namespace OrgBundle\Bus\Complaint\Command;

use AppBundle\Bus\Message\MessageHandler;
use OrgBundle\Entity\Complaint;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CheckComplaintCommandHandler extends MessageHandler
{
    public function handle(CheckComplaintCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var Complaint $complaint
         */
        $complaint = $em->getRepository(Complaint::class)->find($command->id);

        if (!$complaint) {
            throw new NotFoundHttpException('Жалоба не найдена');
        }
        
        if ($complaint->getIsChecked() != boolval($command->value)) {
            $complaint->setIsChecked(boolval($command->value));
        }
    }
}