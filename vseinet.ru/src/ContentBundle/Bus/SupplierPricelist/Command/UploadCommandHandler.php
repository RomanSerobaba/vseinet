<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use SupplyBundle\Entity\Supplier;
use SupplyBundle\Entity\SupplierPricelist;
use ContentBundle\Bus\SupplierPricelist\Parser\ParserFactory;

class UploadCommandHandler extends MessageHandler
{
    public function handle(UploadCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $pricelist = $em->getRepository(SupplierPricelist::class)->find($command->id);
        if (!$pricelist instanceof SupplierPricelist) {
            throw new NotFoundHttpException(sprintf('Прайслист %d не найден', $command->id));
        }

        if (!$command->pricelist instanceof UploadedFile) {
            throw new BadRequestHttpException('Файл не загружен');
        }
        
        $supplier = $em->getRepository(Supplier::class)->find($pricelist->getSupplierId());
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException(sprintf('Поставщик %d не найден', $pricelist->getSupplierId()));
        }

        $parser = $this->get('supplier.pricelist.parser')->init($supplier->getCode(), $pricelist->getName());
        $parser->check($command->pricelist->getPathname());

        $filename = $supplier->getCode().'-temp-'.$pricelist->getId().'.'.$command->pricelist->guessClientExtension();
        $command->pricelist->move($this->getParameter('supplier.pricelist.path'), $filename);

        $pricelist->setUploadStartedAt(new \DateTime('now'));

        $em->persist($pricelist);
        $em->flush();

        $this->get('old_sound_rabbit_mq.execute.command_producer')->publish(json_encode([
            'command' => 'supplier:pricelist:load',
            'args' => [
                'id' => $pricelist->getId(),
                'filename' => $filename, 
            ],
        ]));
    }
}