<?php

namespace ReservesBundle\Bus\GoodsIssueDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsIssueRegister;
use ReservesBundle\Entity\GoodsIssueDoc;
use ReservesBundle\Entity\GoodsIssueDocType;

use ReservesBundle\Bus\GoodsIssueDoc\GoodIssueDocRegistration;

class RegistrationCommandHandler extends MessageHandler
{
    
    public function handle(RegistrationCommand $command) 
    {

        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsIssueDoc::class)->find($command->id);
        if (!$document instanceof GoodsIssueDoc) {
            throw new NotFoundHttpException('Документ не найден (команда)');
        }
        
        GoodIssueDocRegistration::registration($document, $em, $this->get('user.identity')->getUser());
        
    }
    
}
