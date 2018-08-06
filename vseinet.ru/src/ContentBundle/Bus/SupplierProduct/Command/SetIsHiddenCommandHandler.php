<?php 

namespace ContentBundle\Bus\SupplierProduct\Command;

use AppBundle\Bus\Message\MessageHandler;

class SetIsHiddenCommandHandler extends MessageHandler
{
    public function handle(SetIsHiddenCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            UPDATE SupplyBundle:SupplierProduct sp 
            SET sp.isHidden = :isHidden
            WHERE sp.id IN (:ids)
        ");
        $q->setParameter('isHidden', $command->isHidden);
        $q->setParameter('ids', $command->ids);
        $q->execute();

        $em->flush();
    }
}
