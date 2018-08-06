<?php

namespace ReservesBundle\Bus\GoodsAcceptance\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\Query\ResultSetMapping;
use ReservesBundle\Entity\GoodsAcceptance;
use ReservesBundle\Bus\GoodsAcceptance\GoodsAcceptanceUnRegistration;

class UnRegistredCommandHandler extends MessageHandler
{

    public function handle(UnRegistredCommand $command) 
    {
        
        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsAcceptance::class)->find($command->id);
        if (!$document instanceof GoodsAcceptance) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        GoodsAcceptanceUnRegistration::UnRegistration($document, $em, $this->get('user.identity')->getUser());

    }
    
}
