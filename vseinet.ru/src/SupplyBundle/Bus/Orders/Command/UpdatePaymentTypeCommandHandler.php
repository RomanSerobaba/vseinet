<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\MessageHandler;

class UpdatePaymentTypeCommandHandler extends MessageHandler
{
    public function handle(UpdatePaymentTypeCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery('
            UPDATE 
                "order" 
            SET payment_type_code = :payment_type_code 
            WHERE
                id = :order_id
        ');
        $query->setParameter('id', $command->id);
        $query->setParameter('payment_type_code', $command->type);

        $query->execute();
    }
}