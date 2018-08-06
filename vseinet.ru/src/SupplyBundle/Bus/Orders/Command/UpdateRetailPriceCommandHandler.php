<?php 

namespace SupplyBundle\Bus\Orders\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateRetailPriceCommandHandler extends MessageHandler
{
    public function handle(UpdateRetailPriceCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        if ($command->price <= 0) {
            throw new BadRequestHttpException('Недопустимое значение цены');
        }

        $statement = $em->getConnection()->prepare('
            UPDATE client_order_item 
            SET 
                retail_price =  CASE WHEN franchiser_client_price > 0 
                    THEN retail_price 
                    ELSE :price 
                END,
                franchiser_client_price = CASE WHEN franchiser_client_price > 0 
                    THEN :price 
                    ELSE 0 
                END,
                retail_price_updated_at = NOW( ),
                retail_price_updated_by = :user_id::INTEGER 
            WHERE
                order_item_id = :order_item_id 
                AND NOT EXISTS ( SELECT 1 FROM sales_register WHERE order_item_id = id ) 
            RETURNING order_item_id
        ');
        $statement->bindValue('user_id', $currentUser->getId());
        $statement->bindValue('order_item_id', $command->orderItemId);
        $statement->bindValue('price', $command->price);
        $statement->execute();

        $id = $statement->fetchColumn();

        if ($id > 0) {
            throw new BadRequestHttpException('Невозможно изменить цену позиции, по которой произошла продажа');
        }
    }
}