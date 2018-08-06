<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ChangeSupplierInvoiceCounteragentCommandHandler extends MessageHandler
{
    public function handle(ChangeSupplierInvoiceCounteragentCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $sql = '
            UPDATE supply SET supplier_counteragent_id = :counteragent_id WHERE id = :supply_id
        ';

        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('counteragent_id', $command->counteragentId);
        $statement->bindValue('supply_id', $command->id);
        $statement->execute();
    }
}