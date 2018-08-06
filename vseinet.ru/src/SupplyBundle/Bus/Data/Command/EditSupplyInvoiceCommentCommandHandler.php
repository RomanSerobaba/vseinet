<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;

class EditSupplyInvoiceCommentCommandHandler extends MessageHandler
{
    public function handle(EditSupplyInvoiceCommentCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $statement = $em->getConnection()->prepare("
            UPDATE supply SET comment = :comment WHERE id = :supply_id
        ");
        $statement->bindValue('comment', $command->comment);
        $statement->bindValue('supply_id', $command->id);
        $statement->execute();
    }
}