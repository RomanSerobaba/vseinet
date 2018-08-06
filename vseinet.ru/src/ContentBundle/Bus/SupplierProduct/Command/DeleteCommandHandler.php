<?php 

namespace ContentBundle\Bus\SupplierProduct\Command;

use AppBundle\Bus\Message\MessageHandler;

class DeleteCommandHandler extends MessageHandler
{
    public function handle(DeleteCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $q = $em->createQuery("
            DELETE FROM SupplyBundle:SupplierProduct sp 
            WHERE sp.id IN (:ids)
        ");
        $q->setParameter('ids', $command->ids);
        $q->execute();

        $em->flush();
    }
}
