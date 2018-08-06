<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\MessageHandler;
use SupplyBundle\Entity\Supplier;
use AccountingBundle\Entity\Counteragent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EditCounteragentCommandHandler extends MessageHandler
{
    public function handle(EditCounteragentCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $supplier = $em->getRepository(Supplier::class)->findOneBy(['id' => $command->id,]);
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException('Supplier not found');
        }

        $model = $em->getRepository(Counteragent::class)->findOneBy(['id' => $command->cid,]);

        if ($model instanceof Counteragent) {
            $model->setName($command->name);
            $model->setTin($command->tin);
            $model->setKpp($command->kpp);
            $model->setOgrn($command->ogrn);
            $model->setOkpo($command->okpo);
            $model->setVatRate($command->vatRate);

            $em->persist($model);
            $em->flush();
        } else {
            throw new NotFoundHttpException('Counteragent not found');
        }
    }
}