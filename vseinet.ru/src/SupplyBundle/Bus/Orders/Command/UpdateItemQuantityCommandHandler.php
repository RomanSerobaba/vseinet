<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateItemQuantityCommandHandler extends MessageHandler
{
    public function handle(UpdateItemQuantityCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        if ($command->quantity <= 0) {
            throw new BadRequestHttpException('Недопустимое количество');
        }

        $query = $em->createQuery('
            SELECT 
                id 
            FROM
                order_item 
            WHERE
                (supplier_reserve IS NOT NULL OR is_annulled = TRUE) 
                AND is_completed = FALSE 
                AND ID = :order_item_id
        ');
        $query->setParameter('order_item_id', $command->orderItemId);

        $rows = $query->getArrayResult();

        if (!empty($rows)) {
            $query = $em->createQuery('
                UPDATE order_item 
                SET quantity = :new_quantity 
                WHERE
                    id = :order_item_id
            ');
            $query->setParameter('new_quantity', $command->quantity);
            $query->setParameter('order_item_id', $command->orderItemId);

            $query->execute();
        }
    }
}