<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\MessageHandler;
use SupplyBundle\Entity\Supplier;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UnlinkCounteragentCommandHandler extends MessageHandler
{
    public function handle(UnlinkCounteragentCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $supplier = $em->getRepository(Supplier::class)->findOneBy(['id' => $command->id,]);
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException('Supplier not found');
        }

        $query = $em->createQuery("
            DELETE FROM supplier_to_counteragent WHERE supplier_id=:supplier_id AND counteragent_id=:counteragent_id
        ");
        $query->setParameter('supplier_id', $command->id);
        $query->setParameter('counteragent_id', $command->counteragentId);
        $query->execute();

        $query = $em->createQuery("
            UPDATE supplier_to_counteragent 
            SET is_main = FALSE 
            WHERE
                supplier_id = :supplier_id
        ");
        $query->setParameter('supplier_id', $command->id);
        $query->execute();

        $query = $em->createQuery("
            UPDATE supplier_to_counteragent 
            SET is_main = TRUE 
            WHERE
                supplier_id = :supplier_id AND counteragent_id = (SELECT MAX(counteragent_id) FROM supplier_to_counteragent WHERE supplier_id = :id)
        ");
        $query->setParameter('supplier_id', $command->id);
        $query->setParameter('counteragent_id', $command->counteragentId);
        $query->execute();
    }
}