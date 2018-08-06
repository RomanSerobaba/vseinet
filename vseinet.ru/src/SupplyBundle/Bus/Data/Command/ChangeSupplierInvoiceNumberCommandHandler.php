<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ChangeSupplierInvoiceNumberCommandHandler extends MessageHandler
{
    public function handle(ChangeSupplierInvoiceNumberCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $sql = '
            UPDATE supply SET supplier_invoice_number = :number WHERE id = :supply_id
        ';

        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('number', $command->supplierInvoiceNumber, Type::STRING);
        $statement->bindValue('supply_id', $command->id);
        $statement->execute();
    }
}