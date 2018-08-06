<?php 

namespace SupplyBundle\Bus\Data\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\DocumentTypeCode;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Query\ResultSetMapping;
use SupplyBundle\Component\OrderComponent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GetSupplierWithInvoicesQueryHandler extends MessageHandler
{
    public function handle(GetSupplierWithInvoicesQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $component = new OrderComponent($em);

        return $component->getSupplierWithInvoices($query->state, $query->supplierId, $query->fromDate, $query->toDate, $query->supplyId);
    }
}