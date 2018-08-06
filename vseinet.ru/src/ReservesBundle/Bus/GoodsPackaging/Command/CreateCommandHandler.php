<?php

namespace ReservesBundle\Bus\GoodsPackaging\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\GoodsPackagingType;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ReservesBundle\Entity\GoodsPackaging;
use Doctrine\ORM\Query\ResultSetMapping;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $currentUser = $this->get('user.identity')->getUser();

        $em = $this->getDoctrine()->getManager();

        // Получаем номер документа
        
        $queryText = "select nextval('goods_packaging_id_seq'::regclass) as next_number;";
                
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('next_number', 'next_number', 'integer');
        
        $queryDB = $em->createNativeQuery($queryText, $rsm);

        $documentNumber = $queryDB->getResult()[0]['next_number'];
        
        //
        
        $uuid = $command->uuid; unset($command->uuid);
        $item = new GoodsPackaging();
        
        $item->setNumber($documentNumber);
        $item->setGeoRoomId($command->geoRoomId);
        $item->setBaseProductId($command->baseProductId);
        $item->setQuantity($command->quantity);
        $item->setType($command->type);
        
        $item->setCreatedAt(new \DateTime());        
        $item->setCreatedBy($currentUser->getId());
        
        if (empty($command->title)) {
            
            $dateStr = $item->getCreatedAt()->format("d.m.Y");
            $item->setTitle(('combining' == $command->type ? 'Комплектация' : 'Разукомплектация') ." №{$item->getNumber()} от {$dateStr}");
            
        }else{
            
            $item->setTitle($command->title);
                    
        }
        
        $em->persist($item);
        $em->flush();
         
        $this->get('uuid.manager')->saveId($uuid, $item->getId());
    }
}