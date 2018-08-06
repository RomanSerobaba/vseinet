<?php

namespace ReservesBundle\Bus\GoodsDecisionDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use ReservesBundle\Entity\GoodsDecisionDoc;
use ReservesBundle\Bus\GoodsDecisionDoc\GoodsDecisionDocUnRegistration; 

class UnRegistrationCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(UnRegistrationCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsDecisionDoc::class)->find($command->id);
        if (!$document instanceof GoodsDecisionDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        GoodsDecisionDocUnRegistration::UnRegistration($document, $em, $this->get('user.identity')->getUser());

    }
    
}
