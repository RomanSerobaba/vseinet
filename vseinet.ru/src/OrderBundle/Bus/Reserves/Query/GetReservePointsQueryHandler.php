<?php 

namespace OrderBundle\Bus\Reserves\Query;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Enum\GoodsNeedRegisterType;
use Doctrine\ORM\Query\ResultSetMapping;
use OrderBundle\Entity\OrderItem;
use ServiceBundle\Services\ReserveService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GetReservePointsQueryHandler extends MessageHandler
{
    /**
     * @param GetReservePointsQuery $query
     *
     * @return array
     */
    public function handle(GetReservePointsQuery $query) : array
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $orderItem = $em->getRepository(OrderItem::class)->findOneBy(['id' => $query->id,]);
        if (!$orderItem) {
            throw new NotFoundHttpException('Order item not found');
        }

        /**
         * @var ReserveService $reserveService
         */
        $reserveService = $this->get('service.reserve');

        $freeList = $reserveService->getFree($orderItem->getBaseProductId());

        $sql = '
            SELECT SUM( gnr.delta ) AS quantity
            FROM RegisterBundle:GoodsNeedRegister AS gnr
            WHERE gnr.orderItemId = :order_item_id
        ';

        $q = $em->createQuery($sql);
        $q->setParameter('order_item_id', $query->id);

        $rows = $q->getArrayResult();
        $row = array_shift($rows);
        $quantity = $row['quantity'];

        $sql = '
            SELECT SUM( srr.delta ) AS quantity
            FROM SupplyBundle:SupplierReserveRegister AS srr
            WHERE srr.orderItemId = :order_item_id
        ';

        $q = $em->createQuery($sql);
        $q->setParameter('order_item_id', $query->id);

        $rows = $q->getArrayResult();
        $row = array_shift($rows);
        $quantity += $row['quantity'];


        foreach ($freeList as &$free) {
            $free->reservedQuantity = 0;

            if ($free->quantity <= $quantity) {
                $free->reservedQuantity = $free->quantity;
                $quantity -= $free->quantity;
            } else {
                $free->reservedQuantity = $quantity;
                $quantity = 0;
            }
        }

        return $freeList;
    }
}