<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use SupplyBundle\Entity\Supplier;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContactCommandHandler extends MessageHandler
{
    public function handle(ContactCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $supplier = $em->getRepository(Supplier::class)->findOneBy(['id' => $command->id,]);
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException('Supplier not found');
        }

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        if (empty($command->date)) { //disable
            $query = $em->createQuery("
                UPDATE supplier
                SET contract_till = NULL, contract_updated_by = :user_id::INTEGER 
                WHERE
                    supplier_id = :supplier_id
            ");
        } else { //enable
            $query = $em->createQuery("
                UPDATE supplier
                SET contract_till = :date_till, contract_updated_by = :user_id::INTEGER 
                WHERE
                    supplier_id = :supplier_id
            ");
            $query->setParameter('date_till', strtotime($command->date));
        }

        $query->setParameter('supplier_id', $command->id);
        $query->setParameter('user_id', $currentUser->getId());

        $query->execute();
    }
}