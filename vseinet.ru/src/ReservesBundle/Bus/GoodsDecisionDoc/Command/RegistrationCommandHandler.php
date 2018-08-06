<?php

namespace ReservesBundle\Bus\GoodsDecisionDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsDecisionDoc;
use ReservesBundle\Bus\GoodsDecisionDoc\GoodsDecisionDocRegistration;

class RegistrationCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(RegistrationCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsDecisionDoc::class)->find($command->id);
        if (!$document instanceof GoodsDecisionDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        GoodsDecisionDocRegistration::Registration($document, $em, $this->get('user.identity')->getUser());
        
    }
    
}
