<?php

namespace ReservesBundle\Bus\GoodsReleaseDoc\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ReservesBundle\Entity\GoodsReleaseDoc;
use ReservesBundle\Bus\GoodsReleaseDoc\GoodsReleaseDocRegistration;

class RegistrationCommandHandler extends MessageHandler
{
    protected $mySupplay = null;
    
    public function handle(RegistrationCommand $command) 
    {

        $em = $this->getDoctrine()->getManager();
        
        $document = $em->getRepository(GoodsReleaseDoc::class)->find($command->id);
        if (!$document instanceof GoodsReleaseDoc) {
            throw new NotFoundHttpException('Документ не найден');
        }
        
        GoodsReleaseDocRegistration::Registration($document, $em, $this->get('user.identity')->getUser());
        
    }
    
}
