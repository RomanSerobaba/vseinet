<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DocumentTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;
use SupplyBundle\Component\OrderComponent;
use SupplyBundle\Entity\ViewSupply;
use SupplyBundle\Entity\SupplierOrder;

class GetSupplierInvoiceQueryHandler extends MessageHandler
{
    /**
     * @param GetSupplierInvoiceQuery $query
     *
     * @return object
     */
    public function handle(GetSupplierInvoiceQuery $query)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $component = new OrderComponent($em);
        $rows = $component->getSupplierWithInvoices(null, null, null, null, $query->id);

        $supplierInvoice = array_shift($rows);

        return $supplierInvoice;
    }
}