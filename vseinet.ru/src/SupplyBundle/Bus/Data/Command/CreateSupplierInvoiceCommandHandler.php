<?php 

namespace SupplyBundle\Bus\Data\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;

class CreateSupplierInvoiceCommandHandler extends MessageHandler
{
    public function handle(CreateSupplierInvoiceCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $sql = '
            INSERT INTO supply ( created_at, created_by, supplier_id, our_counteragent_id, destination_point_id )
            VALUES( NOW( ), :user_id::INTEGER, :supplier_id::INTEGER, :counteragent_id::INTEGER, :point_id::INTEGER )
            RETURNING id
        ';
        $statement = $em->getConnection()->prepare($sql);
        $statement->bindValue('user_id', $currentUser->getId());
        $statement->bindValue('point_id', $command->pointId);
        $statement->bindValue('supplier_id', $command->supplierId);
        $statement->bindValue('counteragent_id', $command->counteragentId);
        $statement->execute();

        $this->get('uuid.manager')->saveId($command->uuid, $statement->fetchColumn());
    }
}