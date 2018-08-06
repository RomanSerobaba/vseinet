<?php

namespace ReservesBundle\Bus\GoodsAcceptance\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsAcceptance;
use ReservesBundle\Bus\GoodsAcceptance\GoodsAcceptanceRegistration;

class RegistrationCommandHandler extends MessageHandler
{
    
    public function handle(RegistrationCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsAcceptance::class)->find($command->id);
        if (!$goodsAcceptance instanceof GoodsAcceptance) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        GoodsAcceptanceRegistration::Registration($document, $em, $this->get('user.identity')->getUser());
    }
    
}
