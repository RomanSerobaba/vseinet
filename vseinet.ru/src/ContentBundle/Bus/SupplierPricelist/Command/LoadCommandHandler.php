<?php 

namespace ContentBundle\Bus\SupplierPricelist\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use SupplyBundle\Entity\Supplier;
use SupplyBundle\Entity\SupplierPricelist;
use AppBundle\Enum\ProductAvailabilityCode;

class LoadCommandHandler extends MessageHandler
{
    public function handle(LoadCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $pricelist = $em->getRepository(SupplierPricelist::class)->find($command->id);
        if (!$pricelist instanceof SupplierPricelist) {
            throw new NotFoundHttpException(sprintf('Прайслист %d не найден', $command->id));
        }

        $file = new File($command->filename);

        $supplier = $em->getRepository(Supplier::class)->find($pricelist->getSupplierId());
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException(sprintf('Поставщик %d не найден', $pricelist->getSupplierId()));
        }

        $q = $em->createQuery("
            UPDATE SupplyBundle:SupplierProduct sp 
            SET sp.availabilityCode = :outOfStock
            WHERE sp.supplierId = :supplierId
        ");
        $q->setParameter('supplierId', $supplier->getId());
        $q->setParameter('outOfStock', ProductAvailabilityCode::OUT_OF_STOCK);
        $q->execute();

        $start = microtime(true);

        $logger = $this->get('simple.logger')->setName('pricelist', 'suppliers');
        $logger->info(sprintf('%s: %s, загрузка начата', $supplier->getCode(), $pricelist->getName()));
        
        $parser = $this->get('supplier.pricelist.parser')->init($supplier->getCode(), $pricelist->getName());
        $loader = $this->get('supplier.pricelist.loader')->init($supplier, $parser->getStrategy()->getIsKeepCategories());

        $parser->parse($file->getPathname(), function(array $data) use ($loader) {
            $product = $loader->prepare($data);
        });

        $logger->info(sprintf('%s: %s, время загрузки: %0.2f с', $supplier->getCode(), $pricelist->getName(), microtime(true) - $start));

        $em->refresh($pricelist);

        if (null === $pricelist->getUploadStartedAt()) {
            return $logger->info(sprintf('%s: %s, загрузка отменена', $supplier->getCode(), $pricelist->getName()));
        }
        
        $start = microtime(true);

        $loader->flush();

        if (0 == $loader->getUploadedQuantity()) {
            return $logger->warning('%s: %s, не загружено ни одного товара', $supplier->getCode(), $pricelist->getName());
        }
    
        if (in_array($supplier->getCode(), ['ME', 'CIT'])) {
            $q = $em->getConnection()->prepare("
                WITH 
                    data (id, base_product_id) AS (
                        SELECT sp2.id, sp.base_product_id 
                        FROM supplier_product sp 
                        INNER JOIN supplier s ON s.id = sp.supplier_id
                        INNER JOIN supplier_product sp2 ON sp2.code = sp.code 
                        INNER JOIN supplier s2 ON s2.id = sp2.supplier_id
                        WHERE s.code = 'ME' AND s2.code = 'CIT' AND sp.base_product_id IS NOT NULL AND sp2.base_product_id IS NULL 
                    )
                UPDATE supplier_product 
                SET base_product_id = data.base_product_id
                FROM data
                WHERE supplier_product.id = data.id 
            ");
            $q->execute();
            $q = $em->getConnection()->prepare("
                WITH 
                    data (id, base_product_id) AS (
                        SELECT sp2.id, sp.base_product_id
                        FROM supplier_product sp 
                        INNER JOIN supplier s ON s.id = sp.supplier_id
                        INNER JOIN supplier_product sp2 ON sp2.name_hash = sp.name_hash
                        INNER JOIN supplier s2 ON s2.id = sp2.supplier_id
                        WHERE s.code = 'ME' AND s2.code = 'CIT' AND sp.base_product_id IS NOT NULL AND sp2.base_product_id IS NULL 
                    )
                UPDATE supplier_product
                SET base_product_id = data.base_product_id
                FROM data 
                WHERE supplier_product.id = data.id 
            ");
            $q->execute();
        }

        $logger->info(sprintf('%s: %s, время сохранения: %0.2f с, загружено товаров: %d', $supplier->getCode(), $pricelist->getName(), microtime(true) - $start, $loader->getUploadedQuantity()));

        $pricelist->setUploadStartedAt(null);
        $pricelist->setUploadedAt(new \DateTime());
        $pricelist->setUploadedQuantity($loader->getUploadedQuantity());

        $em->persist($pricelist);
        $em->flush();

        $filename = $supplier->getCode().'-'.$pricelist->getId().'.'.$file->guessExtension();
        $file->move($this->getParameter('supplier.pricelist.path'), $filename);

        $this->get('old_sound_rabbit_mq.notify.front_producer')->publish(json_encode([
            'type' => 'supplier:pricelist:loaded',
            'data' => [
                'id' => $pricelist->getId(),
                'uploadedQuantity' => $pricelist->getUploadedQuantity(),
            ],
        ]));
    }
}