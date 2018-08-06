<?php 

namespace ContentBundle\Bus\SupplierPricelist\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SupplyBundle\Entity\Supplier;
use Symfony\Component\Finder\Finder;

class GetListQueryHandler extends MessageHandler
{
    public function handle(GetListQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $supplier = $em->getRepository(Supplier::class)->find($query->supplierId);
        if (!$supplier instanceof Supplier) {
            throw new NotFoundHttpException(sprintf('Поставщик %d не найден', $query->supplierId));
        }

        $where = "";
        if ('active' == $query->filter) {
            $where = " AND spl.isActive = true";
        }

        $q = $em->createQuery("
            SELECT 
                NEW ContentBundle\Bus\SupplierPricelist\Query\DTO\Pricelist (
                    spl.id,
                    spl.name,
                    spl.uploadedAt,
                    spl.uploadedQuantity,
                    spl.isActive,
                    spl.uploadStartedAt
                )
            FROM SupplyBundle:SupplierPricelist spl
            WHERE spl.supplierId = :supplierId {$where} 
            ORDER BY spl.name 
        ");
        $q->setParameter('supplierId', $supplier->getId());
        $pricelists = $q->getArrayResult();

        $files = new Finder();
        foreach ($pricelists as $pricelist) {
            $files->name($supplier->getCode().'-'.$pricelist->id.'*')->in($this->getParameter('supplier.pricelist.path'));
            foreach ($files as $file) {
                $pricelist->filename = $this->getParameter('supplier.pricelist.web.path').$file->getFilename();
                break;                
            }    
        }

        return $pricelists;
    }
}
