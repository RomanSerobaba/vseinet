<?php 

namespace SupplyBundle\Bus\Sms\Command;

use AppBundle\Bus\Message\MessageHandler;
use SupplyBundle\Entity\ViewSupplierOrderItem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;

class SendCommandHandler extends MessageHandler
{
    public function handle(SendCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        // $em = $this->getDoctrine()->getManager();

        // $query = $em->createQuery('
        //     INSERT INTO order_comment (
        //       order_id, 
        //       order_item_id, 
        //       "text", 
        //       created_by, 
        //       created_at, 
        //       "type" 
        //     ) VALUES (
        //       :order_id::INTEGER, 
        //       :order_item_id::INTEGER, 
        //       :text, 
        //       :user_id::INTEGER, 
        //       NOW(), 
        //       :type
        //     )
        // ');
        // $query->setParameter('order_id', $command->id);
        // $query->setParameter('order_item_id', $command->orderItemId);
        // $query->setParameter('text', $command->text);
        // $query->setParameter('user_id', $command->userId);
        // $query->setParameter('type', \OrderBundle\Entity\OrderComment::TYPE_MANAGER);

        // $query->execute();
    }
}