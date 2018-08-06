<?php 

namespace SupplyBundle\Bus\Suppliers\Command;

use AppBundle\Bus\Message\MessageHandler;
use SupplyBundle\Entity\Supplier;
use Doctrine\ORM\EntityNotFoundException;
use AccountingBundle\Entity\Counteragent;
use SupplyBundle\Entity\SupplierToCounteragent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NewCounteragentCommandHandler extends MessageHandler
{
    public function handle(NewCounteragentCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $supplier = $em->getRepository(Supplier::class)->findOneBy(['id' => $command->id,]);
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException('Supplier not found');
        }

        $model = $em->getRepository(Counteragent::class)->findOneBy(['tin' => $command->tin,]);
        if (empty($model)) {
            $model = new Counteragent();
        }

        $model->setName($command->name);
        $model->setTin($command->tin);
        $model->setKpp($command->kpp);
        $model->setOgrn($command->ogrn);
        $model->setOkpo($command->okpo);
        $model->setVatRate($command->vatRate);

        $em->persist($model);
        $em->flush();

        if ($model->getId()) {
            $linksCount = $em->getRepository(SupplierToCounteragent::class)->count(['supplier_id' => $command->id,]);

            $link = new SupplierToCounteragent();
            $link->setSupplierId($command->id);
            $link->setCounteragentId($model->getId());
            $link->setIsActive(empty($linksCount));
            $link->setIsMain(empty($linksCount));

            $em->persist($link);
            $em->flush();
        } else {
            throw new EntityNotFoundException('Ошибка сохранения юр. лица');
        }
    }
}