<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\MessageHandler;
use SupplyBundle\Entity\Supplier;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AssignSupplierCommandHandler extends MessageHandler
{
    public function handle(AssignSupplierCommand $command)
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
            UPDATE supplier 
            SET manager_id = :manager_id 
            WHERE
                id = :supplier_id
        ");
        $query->setParameter('manager_id', $command->managerId);
        $query->setParameter('supplier_id', $command->id);

        $query->execute();
    }
}