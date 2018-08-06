<?php

namespace ReservesBundle\Bus\GoodsIssueDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
USE Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsIssueDoc;
use ReservesBundle\Entity\GoodsIssueRegister;

use ReservesBundle\Bus\GoodsIssueDoc\GoodIssueDocUnRegistration;

class UnRegistrationCommandHandler extends MessageHandler
{
    
    public function handle(UnRegistrationCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsIssueDoc::class)->find($command->id);
        if (!$document instanceof GoodsIssueDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        GoodIssueDocUnRegistration::unRegistration($document, $em, $this->get('user.identity')->getUser());
        
    }
    
}
