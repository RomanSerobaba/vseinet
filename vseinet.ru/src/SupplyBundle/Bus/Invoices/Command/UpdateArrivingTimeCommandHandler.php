<?php 

namespace SupplyBundle\Bus\Invoices\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UpdateArrivingTimeCommandHandler extends MessageHandler
{
    public function handle(UpdateArrivingTimeCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $sql = '
            UPDATE supplier_invoice 
            SET arriving_time = :time 
            WHERE
                id = :invoiceId     
        ';

        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('invoiceId', $command->id);
        $statement->bindValue('time', $command->time);
        $statement->execute();
    }
}