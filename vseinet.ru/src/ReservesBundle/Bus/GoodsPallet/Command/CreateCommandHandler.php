<?php

namespace ReservesBundle\Bus\GoodsPallet\Command;

use AppBundle\Bus\Message\MessageHandler;
use ReservesBundle\Entity\GoodsPallet;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OrgBundle\Entity\GeoPoint;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();

        $item = new GoodsPallet();

        $currentUser = $this->get('user.identity')->getUser();
        
        $item->setCreatedAt(new \DateTime);
        $item->setCreatedBy($currentUser->getId());
        $item->setStatus(\AppBundle\Enum\GoodsPalletStatusType::FREE);
        $item->setGeoPointId($command->geoPointId);
        
        if (empty($command->title)) {

            $geoPointId = $em->getRepository(GeoPoint::class)->find($command->geoPointId);
            if (!$geoPointId instanceof GeoPoint) {
                throw new NotFoundHttpException('Геоточка не найдена.');
            }

            $item->setTitle($geoPointId->getName() .' ('. $geoPointId->getCode() .')');
            
        }else{
            
            $item->setTitle($command->title);
            
        }
        
        $em->persist($item);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $item->getId());

        return;            
    }
    
}