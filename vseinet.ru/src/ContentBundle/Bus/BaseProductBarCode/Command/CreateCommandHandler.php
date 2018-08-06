<?php

namespace ContentBundle\Bus\BaseProductBarCode\Command;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\BaseProductBarCode;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $em = $this->getDoctrine()->getManager();
        
        $item = new BaseProductBarCode();
        $item->setBarCode($command->barCode);
        $item->setBarCodeType($command->barCodeType ? $command->barCodeType : BaseProductBarCode::calcBarCodeType($command->barCode));
        $item->setBaseProductId($command->baseProductId);
        $item->setGoodsPalletId($command->goodsPalletId);
        
        $em->persist($item);
        $em->flush();
    }
}