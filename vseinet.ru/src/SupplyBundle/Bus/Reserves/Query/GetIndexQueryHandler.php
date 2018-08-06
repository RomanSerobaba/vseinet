<?php 

namespace SupplyBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\MessageHandler;
use SupplyBundle\Entity\Supplier;
use SupplyBundle\Component\OrderComponent;
use SupplyBundle\Entity\SupplierReserve;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetIndexQueryHandler extends MessageHandler
{
    /**
     * @param GetIndexQuery $query
     *
     * @return array
     */
    public function handle(GetIndexQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $supplierReserve = $em->getRepository(SupplierReserve::class)->find($query->id);
        if (!$supplierReserve instanceof SupplierReserve) {
            throw new NotFoundHttpException('SupplierReserve не найден');
        }

        $component = new OrderComponent($em);

        return $component->getSupplierProducts((int)$query->id, $this->getParameter('product.images.web.path'), $query->pointId, $query->withConfirmedReserves);
    }
}